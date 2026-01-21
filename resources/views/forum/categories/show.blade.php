@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-dark p-2 rounded">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-success">Forum</a></li>
                <li class="breadcrumb-item active text-light">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>{{ $category->name }}</h2>
                <p class="text-muted">{{ $category->description }}</p>
            </div>
            @auth
                <a href="{{ route('topics.create', $category) }}" class="btn btn-success">+ New Topic</a>
            @endauth
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($topics->isEmpty())
            <div class="alert alert-info">No topics in this category yet. @auth Be the first to create one! @endauth</div>
        @else
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th>Topic</th>
                        <th>Author</th>
                        <th>Replies</th>
                        <th>Last Activity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                        <tr>
                            <td>
                                <a href="{{ route('topics.show', $topic) }}" class="text-success text-decoration-none">
                                    {{ $topic->title }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('profile.show', $topic->user) }}" class="text-success text-decoration-none">
                                    {{ $topic->user->name }}
                                </a>
                            </td>
                            <td>{{ $topic->posts->count() - 1 }}</td>
                            <td>{{ $topic->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
@endsection
