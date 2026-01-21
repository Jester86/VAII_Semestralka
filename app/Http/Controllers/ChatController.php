<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get chat messages (AJAX).
     */
    public function getMessages(Request $request)
    {
        $afterId = $request->query('after_id', 0);
        $sync = $request->query('sync', false);

        $query = ChatMessage::with('user')
            ->orderBy('id', 'desc')
            ->limit(50);

        if ($afterId > 0 && !$sync) {
            $messages = ChatMessage::with('user')
                ->where('id', '>', $afterId)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $messages = $query->get()->reverse()->values();
        }

        // Get all current message IDs for sync (to detect deletions)
        $allMessageIds = ChatMessage::orderBy('id', 'desc')
            ->limit(50)
            ->pluck('id')
            ->toArray();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return $this->formatMessage($message);
            }),
            'message_ids' => $allMessageIds,
            'count' => $messages->count(),
        ]);
    }

    /**
     * Store a new chat message (AJAX).
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $message->load('user');

        return response()->json([
            'success' => true,
            'message' => $this->formatMessage($message),
        ]);
    }

    /**
     * Delete a chat message (AJAX).
     */
    public function destroy(ChatMessage $chatMessage)
    {
        $user = Auth::user();

        if ($user->id !== $chatMessage->user_id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $chatMessage->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $chatMessage->id,
        ]);
    }

    /**
     * Format a message for JSON response.
     */
    private function formatMessage(ChatMessage $message)
    {
        return [
            'id' => $message->id,
            'message' => e($message->message),
            'user_id' => $message->user_id,
            'username' => $message->user->name ?? 'Unknown',
            'user_role' => $message->user->role ?? 'user',
            'created_at' => $message->created_at->format('M d, Y H:i'),
            'time_ago' => $message->created_at->diffForHumans(),
            'can_delete' => Auth::check() && (Auth::id() === $message->user_id || Auth::user()->role === 'admin'),
        ];
    }
}
