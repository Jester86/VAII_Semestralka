@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-dark p-2 rounded">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-success">Forum</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $topic->category) }}" class="text-success">{{ $topic->category->name }}</a></li>
                <li class="breadcrumb-item active text-light">{{ $topic->title }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $topic->title }}</h2>
            @auth
                @if(Auth::id() === $topic->user_id || Auth::user()->role === 'admin')
                    <div>
                        <a href="{{ route('topics.edit', $topic) }}" class="btn btn-sm btn-primary">Edit Topic</a>
                        <form action="{{ route('topics.destroy', $topic) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this topic and all its posts?')">Delete Topic</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Posts Container --}}
        <div id="posts-container">
            @foreach($posts as $post)
                <div class="card bg-dark border-secondary mb-3 post-card" data-post-id="{{ $post->id }}" data-updated-at="{{ $post->updated_at->timestamp }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('profile.show', $post->user) }}" class="text-success fw-bold text-decoration-none">{{ $post->user->name }}</a>
                            <span class="badge bg-secondary ms-1">{{ $post->user->reputation }} rep</span>
                            <small class="text-muted ms-2">{{ $post->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        @auth
                            @if(Auth::id() === $post->user_id || Auth::user()->role === 'admin')
                                <div>
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')">Delete</button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                    <div class="card-body">
                        <div class="card-text text-light post-content">{!! \App\Helpers\ContentRenderer::render($post->content) !!}</div>

                        {{-- Display Attachments --}}
                        @if($post->attachments->count() > 0)
                            <div class="attachments mt-3 pt-3 border-top border-secondary">
                                <small class="text-muted d-block mb-2">Attachments:</small>
                                <div class="row g-2">
                                    @foreach($post->attachments as $attachment)
                                        <div class="col-auto">
                                            @if($attachment->isImage())
                                                <a href="{{ $attachment->url }}" target="_blank" class="d-block">
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="img-thumbnail" style="max-height: 150px; max-width: 200px;">
                                                </a>
                                            @elseif($attachment->mime_type === 'image/gif')
                                                <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                            @elseif($attachment->isVideo())
                                                <video controls class="rounded" style="max-height: 200px; max-width: 300px;">
                                                    <source src="{{ $attachment->url }}" type="{{ $attachment->mime_type }}">
                                                </video>
                                            @else
                                                <a href="{{ $attachment->url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                    {{ $attachment->original_filename }} ({{ $attachment->human_size }})
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mb-4">
            {{ $posts->links() }}
        </div>

        {{-- Reply Form - Only for authenticated users --}}
        @auth
            <div class="card bg-dark border-success">
                <div class="card-header">
                    <h5 class="mb-0">Post a Reply</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('posts.store', $topic) }}" enctype="multipart/form-data" id="reply-form">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" name="content" id="reply-content" rows="4" placeholder="Write your reply..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Attach files (images, documents, etc.)</label>
                            <input type="file" class="form-control" name="attachments[]" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip">
                            <small class="text-muted">Max 10MB per file. Allowed: images, GIFs, videos, PDF, DOC, TXT, ZIP</small>
                        </div>
                        <button type="submit" class="btn btn-success">Post Reply</button>
                    </form>
                </div>
            </div>
        @else
            <div class="card bg-dark border-secondary">
                <div class="card-body text-center">
                    <p class="text-muted mb-2">You must be logged in to reply to this topic.</p>
                    <a href="{{ route('login') }}" class="btn btn-success">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-success ms-2">Register</a>
                </div>
            </div>
        @endauth
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const topicId = {{ $topic->id }};
    const postsContainer = document.getElementById('posts-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let pollingInterval = 5000; // Check every 5 seconds

    // Store current post states (id -> updated_at timestamp)
    let postStates = {};

    // Initialize post states from existing posts
    function initPostStates() {
        postsContainer.querySelectorAll('.post-card').forEach(card => {
            const postId = parseInt(card.dataset.postId);
            const updatedAt = parseInt(card.dataset.updatedAt);
            postStates[postId] = updatedAt;
        });
    }

    function syncPosts() {
        fetch(`/api/topics/${topicId}/posts/sync`)
            .then(response => response.json())
            .then(data => {
                const serverPostIds = data.post_ids;
                const serverPosts = {};

                // Build a map of server posts
                data.posts.forEach(post => {
                    serverPosts[post.id] = post;
                });

                // Check for deleted posts
                const currentPostIds = Object.keys(postStates).map(id => parseInt(id));
                currentPostIds.forEach(postId => {
                    if (!serverPostIds.includes(postId)) {
                        // Post was deleted
                        removePost(postId);
                        delete postStates[postId];
                    }
                });

                // Check for new or edited posts
                data.posts.forEach(post => {
                    const existingState = postStates[post.id];

                    if (!existingState) {
                        // New post - add it
                        addNewPost(post);
                        postStates[post.id] = post.updated_at;
                    } else if (existingState < post.updated_at) {
                        // Post was edited - update it
                        updatePost(post);
                        postStates[post.id] = post.updated_at;
                    }
                });
            })
            .catch(error => console.error('Error syncing posts:', error));
    }

    function removePost(postId) {
        const postCard = postsContainer.querySelector(`.post-card[data-post-id="${postId}"]`);
        if (postCard) {
            postCard.style.transition = 'opacity 0.3s, transform 0.3s';
            postCard.style.opacity = '0';
            postCard.style.transform = 'translateX(-20px)';
            setTimeout(() => postCard.remove(), 300);
        }
    }

    function updatePost(post) {
        const postCard = postsContainer.querySelector(`.post-card[data-post-id="${post.id}"]`);
        if (postCard) {
            // Update the content
            const contentDiv = postCard.querySelector('.post-content');
            if (contentDiv) {
                contentDiv.innerHTML = post.content;
            }

            // Update attachments
            updateAttachments(postCard, post);

            // Update the data attribute
            postCard.dataset.updatedAt = post.updated_at;

            // Highlight the updated post
            postCard.classList.add('post-updated');
            setTimeout(() => postCard.classList.remove('post-updated'), 2000);
        }
    }

    function updateAttachments(postCard, post) {
        // Remove existing attachments section
        const existingAttachments = postCard.querySelector('.attachments');
        if (existingAttachments) {
            existingAttachments.remove();
        }

        // Add new attachments if any
        if (post.attachments && post.attachments.length > 0) {
            const cardBody = postCard.querySelector('.card-body');
            const attachmentsHtml = createAttachmentsHtml(post.attachments);
            cardBody.insertAdjacentHTML('beforeend', attachmentsHtml);
        }
    }

    function addNewPost(post) {
        const postHtml = createPostHtml(post);
        postsContainer.insertAdjacentHTML('beforeend', postHtml);

        // Scroll to the new post
        const newPostCard = postsContainer.querySelector(`.post-card[data-post-id="${post.id}"]`);
        if (newPostCard) {
            newPostCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function createAttachmentsHtml(attachments) {
        return `
            <div class="attachments mt-3 pt-3 border-top border-secondary">
                <small class="text-muted d-block mb-2">Attachments:</small>
                <div class="row g-2">
                    ${attachments.map(att => {
                        if (att.is_image) {
                            return `<div class="col-auto">
                                <a href="${att.url}" target="_blank" class="d-block">
                                    <img src="${att.url}" alt="${att.original_filename}" class="img-thumbnail" style="max-height: 150px; max-width: 200px;">
                                </a>
                            </div>`;
                        } else if (att.is_video) {
                            return `<div class="col-auto">
                                <video controls class="rounded" style="max-height: 200px; max-width: 300px;">
                                    <source src="${att.url}" type="${att.mime_type}">
                                </video>
                            </div>`;
                        } else {
                            return `<div class="col-auto">
                                <a href="${att.url}" target="_blank" class="btn btn-sm btn-outline-success">
                                    ${att.original_filename} (${att.human_size})
                                </a>
                            </div>`;
                        }
                    }).join('')}
                </div>
            </div>`;
    }

    function createPostHtml(post) {
        let attachmentsHtml = '';
        if (post.attachments && post.attachments.length > 0) {
            attachmentsHtml = createAttachmentsHtml(post.attachments);
        }

        let editButtons = '';
        if (post.can_edit) {
            editButtons = `
                <div>
                    <a href="${post.edit_url}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="${post.delete_url}" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')">Delete</button>
                    </form>
                </div>`;
        }

        return `
            <div class="card bg-dark border-secondary mb-3 post-card new-post" data-post-id="${post.id}" data-updated-at="${post.updated_at}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="${post.user.profile_url}" class="text-success fw-bold text-decoration-none">${post.user.name}</a>
                        <span class="badge bg-secondary ms-1">${post.user.reputation} rep</span>
                        <small class="text-muted ms-2">${post.created_at}</small>
                    </div>
                    ${editButtons}
                </div>
                <div class="card-body">
                    <div class="card-text text-light post-content">${post.content}</div>
                    ${attachmentsHtml}
                </div>
            </div>`;
    }

    // Initialize and start polling
    initPostStates();
    setInterval(syncPosts, pollingInterval);
});
</script>

<style>
.post-card.new-post {
    animation: slideIn 0.5s ease-out;
    border-color: #198754 !important;
}

.post-card.post-updated {
    animation: highlightUpdate 2s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes highlightUpdate {
    0% { background-color: rgba(13, 202, 240, 0.3); }
    100% { background-color: transparent; }
}
</style>
@endsection
