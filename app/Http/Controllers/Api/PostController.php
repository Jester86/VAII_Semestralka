<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\ContentRenderer;

class PostController extends Controller
{
    /**
     * Get posts for a topic, optionally after a specific post ID.
     */
    public function index(Request $request, Topic $topic)
    {
        $afterId = $request->query('after_id', 0);

        $query = $topic->posts()
            ->with(['user', 'attachments'])
            ->orderBy('id', 'asc');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $posts = $query->get();

        return response()->json([
            'posts' => $posts->map(function ($post) {
                return $this->formatPost($post);
            }),
            'count' => $posts->count(),
        ]);
    }

    /**
     * Get all current post IDs for a topic (to detect deletions).
     */
    public function sync(Request $request, Topic $topic)
    {
        $posts = $topic->posts()
            ->with(['user', 'attachments'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'posts' => $posts->map(function ($post) {
                return $this->formatPost($post);
            }),
            'post_ids' => $posts->pluck('id')->toArray(),
            'count' => $posts->count(),
        ]);
    }

    /**
     * Get the count of posts after a specific ID.
     */
    public function count(Request $request, Topic $topic)
    {
        $afterId = $request->query('after_id', 0);

        $count = $topic->posts()
            ->where('id', '>', $afterId)
            ->count();

        return response()->json([
            'count' => $count,
            'topic_id' => $topic->id,
        ]);
    }

    /**
     * Format a post for JSON response.
     */
    private function formatPost(Post $post): array
    {
        return [
            'id' => $post->id,
            'content' => ContentRenderer::render($post->content),
            'content_raw' => $post->content,
            'updated_at' => $post->updated_at->timestamp,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'reputation' => $post->user->reputation,
                'profile_url' => route('profile.show', $post->user),
            ],
            'created_at' => $post->created_at->format('M d, Y H:i'),
            'attachments' => $post->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'url' => $attachment->url,
                    'original_filename' => $attachment->original_filename,
                    'mime_type' => $attachment->mime_type,
                    'human_size' => $attachment->human_size,
                    'is_image' => $attachment->isImage(),
                    'is_video' => $attachment->isVideo(),
                ];
            }),
            'can_edit' => auth()->check() && (auth()->id() === $post->user_id || auth()->user()->role === 'admin'),
            'edit_url' => route('posts.edit', $post),
            'delete_url' => route('posts.destroy', $post),
        ];
    }
}
