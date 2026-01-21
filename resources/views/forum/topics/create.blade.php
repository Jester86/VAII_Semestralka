@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-dark p-2 rounded">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-success">Forum</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $category) }}" class="text-success">{{ $category->name }}</a></li>
                <li class="breadcrumb-item active text-light">New Topic</li>
            </ol>
        </nav>

        <h2>Create New Topic</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('topics.store', $category) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Topic Title</label>
                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="6" required>{{ old('content') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted">Attachments</label>
                <input type="file" class="form-control" name="attachments[]" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip">
                <small class="text-muted">Max 10MB per file. Allowed: images, GIFs, videos, PDF, DOC, TXT, ZIP</small>
            </div>

            <button type="submit" class="btn btn-success">Create Topic</button>
            <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
