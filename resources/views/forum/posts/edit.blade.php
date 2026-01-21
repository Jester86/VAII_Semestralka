@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Edit Post</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="6" required>{{ old('content', $post->content) }}</textarea>
            </div>

            {{-- Existing Attachments --}}
            @if($post->attachments->count() > 0)
                <div class="mb-3">
                    <label class="form-label text-muted">Current Attachments</label>
                    <div class="row g-2">
                        @foreach($post->attachments as $attachment)
                            <div class="col-auto">
                                <div class="card bg-dark border-secondary p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="delete_{{ $attachment->id }}">
                                        <label class="form-check-label text-danger" for="delete_{{ $attachment->id }}">
                                            Delete
                                        </label>
                                    </div>
                                    @if($attachment->isImage())
                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->original_filename }}" class="img-thumbnail mt-1" style="max-height: 100px; max-width: 150px;">
                                    @else
                                        <span class="text-muted small">{{ $attachment->original_filename }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Add New Attachments --}}
            <div class="mb-3">
                <label class="form-label text-muted">Add New Attachments</label>
                <input type="file" class="form-control" name="attachments[]" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip">
                <small class="text-muted">Max 10MB per file. Allowed: images, GIFs, videos, PDF, DOC, TXT, ZIP</small>
            </div>

            <button type="submit" class="btn btn-success">Update Post</button>
            <a href="{{ route('topics.show', $post->topic_id) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
