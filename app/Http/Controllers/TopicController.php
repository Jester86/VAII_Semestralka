<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Category;
use App\Models\Post;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    public function show(Topic $topic)
    {
        $posts = $topic->posts()->with(['user', 'attachments'])->oldest()->paginate(20);
        return view('forum.topics.show', compact('topic', 'posts'));
    }

    public function create(Category $category)
    {
        return view('forum.topics.create', compact('category'));
    }

    public function store(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,webm,pdf,doc,docx,txt,zip',
        ]);

        $topic = Topic::create([
            'title' => $request->title,
            'category_id' => $category->id,
            'user_id' => Auth::id(),
        ]);

        // Create the first post in the topic
        $post = Post::create([
            'content' => $request->content,
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
        ]);

        // Handle attachments for the first post
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

        return redirect()->route('topics.show', $topic)->with('success', 'Topic created successfully.');
    }

    public function edit(Topic $topic)
    {
        // Only allow topic owner or admin to edit
        if (Auth::id() !== $topic->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('forum.topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic)
    {
        // Only allow topic owner or admin to update
        if (Auth::id() !== $topic->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $topic->update($request->only('title'));

        return redirect()->route('topics.show', $topic)->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        // Only allow topic owner or admin to delete
        if (Auth::id() !== $topic->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Delete all attachments from posts in this topic
        foreach ($topic->posts as $post) {
            foreach ($post->attachments as $attachment) {
                Storage::disk('public')->delete('attachments/' . $attachment->filename);
            }
        }

        $categoryId = $topic->category_id;
        $topic->delete();

        return redirect()->route('categories.show', $categoryId)->with('success', 'Topic deleted successfully.');
    }
}
