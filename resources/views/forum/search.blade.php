@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success">Search Results</h2>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-success">Back to Forum</a>
        </div>

        {{-- Search Bar --}}
        <form action="{{ route('forum.search') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text"
                       name="q"
                       class="form-control bg-dark text-success border-success"
                       placeholder="Search topics, posts, categories..."
                       value="{{ $query }}"
                       minlength="2"
                       required>
                <button class="btn btn-success" type="submit">
                    Search
                </button>
            </div>
        </form>

        <p class="text-muted mb-4">
            Found {{ $topics->count() }} topics, {{ $posts->count() }} posts, and {{ $categories->count() }} categories for "<strong class="text-success">{{ $query }}</strong>"
        </p>

        {{-- Categories Results --}}
        @if($categories->isNotEmpty())
            <div class="card bg-dark border-success mb-4">
                <div class="card-header border-success">
                    <h5 class="mb-0 text-success">Categories ({{ $categories->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-black rounded border border-secondary">
                                    <h6>
                                        <a href="{{ route('categories.show', $category) }}" class="text-success text-decoration-none">
                                            {{ $category->name }}
                                        </a>
                                    </h6>
                                    <p class="text-light mb-1 small">{{ Str::limit($category->description, 100) }}</p>
                                    <small class="text-muted">{{ $category->topics_count }} topics</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Topics Results --}}
        @if($topics->isNotEmpty())
            <div class="card bg-dark border-success mb-4">
                <div class="card-header border-success">
                    <h5 class="mb-0 text-success">Topics ({{ $topics->count() }})</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($topics as $topic)
                        <li class="list-group-item bg-dark border-secondary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('topics.show', $topic) }}" class="text-success text-decoration-none">
                                            {{ $topic->title }}
                                        </a>
                                    </h6>
                                    <p class="text-light mb-1 small">{{ Str::limit(strip_tags($topic->content), 150) }}</p>
                                    <small class="text-muted">
                                        in <a href="{{ route('categories.show', $topic->category) }}" class="text-success">{{ $topic->category->name }}</a>
                                        • by <a href="{{ route('profile.show', $topic->user) }}" class="text-success">{{ $topic->user->name }}</a>
                                        • {{ $topic->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Posts Results --}}
        @if($posts->isNotEmpty())
            <div class="card bg-dark border-success mb-4">
                <div class="card-header border-success">
                    <h5 class="mb-0 text-success">Posts ({{ $posts->count() }})</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($posts as $post)
                        <li class="list-group-item bg-dark border-secondary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-light mb-1 small">{{ Str::limit(strip_tags($post->content), 150) }}</p>
                                    <small class="text-muted">
                                        in topic <a href="{{ route('topics.show', $post->topic) }}" class="text-success">{{ $post->topic->title }}</a>
                                        • by <a href="{{ route('profile.show', $post->user) }}" class="text-success">{{ $post->user->name }}</a>
                                        • {{ $post->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- No Results --}}
        @if($categories->isEmpty() && $topics->isEmpty() && $posts->isEmpty())
            <div class="alert alert-info">
                No results found for "<strong>{{ $query }}</strong>". Try different keywords.
            </div>
        @endif
    </div>
@endsection

