<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function inbox()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->with('sender')
            ->latest()
            ->paginate(15);

        return view('messages.inbox', compact('messages'));
    }

    public function sent()
    {
        $messages = Message::where('sender_id', Auth::id())
            ->with('receiver')
            ->latest()
            ->paginate(15);

        return view('messages.sent', compact('messages'));
    }

    public function show(Message $message)
    {
        // Only sender or receiver can view
        if (Auth::id() !== $message->sender_id && Auth::id() !== $message->receiver_id) {
            abort(403);
        }

        // Mark as read if receiver is viewing
        if (Auth::id() === $message->receiver_id && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        $message->load('attachments');

        return view('messages.show', compact('message'));
    }

    public function create(User $user = null)
    {
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        return view('messages.create', compact('users', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,webm,pdf,doc,docx,txt,zip',
        ]);

        // Cannot send message to yourself
        if ((int) $request->receiver_id === Auth::id()) {
            return back()->with('error', 'You cannot send a message to yourself.');
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'content' => $request->content,
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
                    'attachable_type' => Message::class,
                    'attachable_id' => $message->id,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('messages.sent')->with('success', 'Message sent successfully.');
    }

    public function destroy(Message $message)
    {
        // Only sender or receiver can delete
        if (Auth::id() !== $message->sender_id && Auth::id() !== $message->receiver_id) {
            abort(403);
        }

        // Delete attachments from storage
        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete('attachments/' . $attachment->filename);
        }

        $message->delete();

        return redirect()->route('messages.inbox')->with('success', 'Message deleted.');
    }

    public function reply(Message $message)
    {
        // Only receiver can reply
        if (Auth::id() !== $message->receiver_id) {
            abort(403);
        }

        $replyTo = $message->sender;
        $subject = 'Re: ' . $message->subject;

        return view('messages.create', [
            'users' => User::where('id', '!=', Auth::id())->orderBy('name')->get(),
            'user' => $replyTo,
            'subject' => $subject,
        ]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $users = User::where('id', '!=', Auth::id())
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($users);
    }
}
