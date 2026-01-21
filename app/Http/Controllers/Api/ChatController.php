<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get chat messages, optionally after a specific message ID.
     */
    public function index(Request $request)
    {
        $afterId = $request->query('after_id', 0);
        $limit = $request->query('limit', 50);

        $query = ChatMessage::with('user')
            ->orderBy('id', 'desc')
            ->limit($limit);

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get()->reverse()->values();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return $this->formatMessage($message);
            }),
            'count' => $messages->count(),
        ]);
    }

    /**
     * Get new messages after a specific ID (for polling).
     */
    public function poll(Request $request)
    {
        $afterId = $request->query('after_id', 0);

        $messages = ChatMessage::with('user')
            ->where('id', '>', $afterId)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return $this->formatMessage($message);
            }),
            'count' => $messages->count(),
        ]);
    }

    /**
     * Store a new chat message.
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
     * Delete a chat message (admin only or own message).
     */
    public function destroy(ChatMessage $chatMessage)
    {
        $user = Auth::user();

        if (!$user || ($user->id !== $chatMessage->user_id && $user->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $chatMessage->delete();

        return response()->json([
            'success' => true,
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
        ];
    }
}

