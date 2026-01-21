<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function store(Request $request, Topic $topic)
    {
        $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,webm,pdf,doc,docx,txt,zip',
        ]);

        $post = Post::create([
            'content' => $request->content,
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('attachments', $filename, 'public');

                Attachment::create([
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'attachable_type' => Post::class,
                    'attachable_id' => $post->id,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('topics.show', $topic)->with('success', 'Reply posted successfully.');
    }

    public function edit(Post $post)
    {
        // Only allow post owner or admin to edit
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $post->load('attachments');

        return view('forum.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        // Only allow post owner or admin to update
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,webm,pdf,doc,docx,txt,zip',
            'delete_attachments' => 'nullable|array',
            'delete_attachments.*' => 'exists:attachments,id',
        ]);

        $post->update($request->only('content'));

        // Delete selected attachments
        if ($request->has('delete_attachments')) {
            $attachmentsToDelete = Attachment::whereIn('id', $request->delete_attachments)
                ->where('attachable_id', $post->id)
                ->where('attachable_type', Post::class)
                ->get();

            foreach ($attachmentsToDelete as $attachment) {
                Storage::disk('public')->delete('attachments/' . $attachment->filename);
                $attachment->delete();
            }
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('attachments', $filename, 'public');

                Attachment::create([
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'attachable_type' => Post::class,
                    'attachable_id' => $post->id,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('topics.show', $post->topic_id)->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        // Only allow post owner or admin to delete
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Delete attachments from storage
        foreach ($post->attachments as $attachment) {
            Storage::disk('public')->delete('attachments/' . $attachment->filename);
        }

        $topicId = $post->topic_id;
        $post->delete();

        return redirect()->route('topics.show', $topicId)->with('success', 'Post deleted successfully.');
    }
}
