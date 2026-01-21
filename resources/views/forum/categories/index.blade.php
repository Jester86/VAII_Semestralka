@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Forum Categories</h2>
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('categories.create') }}" class="btn btn-success">+ New Category</a>
                @endif
            @endauth
        </div>

        {{-- Search Bar --}}
        <form action="{{ route('forum.search') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text"
                       name="q"
                       class="form-control bg-dark text-success border-success"
                       placeholder="Search topics, posts, categories..."
                       value="{{ request('q') }}"
                       minlength="2"
                       required>
                <button class="btn btn-success" type="submit">
                    Search
                </button>
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($categories->isEmpty())
            <div class="alert alert-info">No categories yet. @auth @if(Auth::user()->role === 'admin') Create one to get started! @endif @endauth</div>
        @else
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-md-6 mb-4">
                        <div class="card bg-dark border-success">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('categories.show', $category) }}" class="text-success text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                </h5>
                                <p class="card-text text-light">{{ $category->description ?? 'No description' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $category->topics_count }} topics</small>
                                    @auth
                                        @if(Auth::user()->role === 'admin')
                                            <div>
                                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category and all its topics?')">Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
