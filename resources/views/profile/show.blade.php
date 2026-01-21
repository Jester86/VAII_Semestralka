@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                {{-- Profile Card --}}
                <div class="card bg-dark border-success mb-4">
                    <div class="card-header text-success">
                        <h4 class="mb-0">{{ $user->name }}</h4>
                    </div>
                    <div class="card-body text-success">
                        <p><strong>Role:</strong> <span class="badge {{ $user->role === 'admin' ? 'bg-warning' : 'bg-secondary' }}">{{ ucfirst($user->role) }}</span></p>
                        <p><strong>Reputation:</strong> <span class="badge bg-success">{{ $user->reputation }}</span></p>
                        <p><strong>Member since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                        <p><strong>Topics:</strong> {{ $topicsCount }}</p>
                        <p><strong>Posts:</strong> {{ $postsCount }}</p>

                        @if(session('success'))
                            <div class="alert alert-success mt-3">{{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                        @endif

                        {{-- Reputation Voting - Only for authenticated users --}}
                        @auth
                            @if(Auth::id() !== $user->id)
                                <div class="mt-3">
                                    <p><strong>Rate this user:</strong></p>
                                    <div class="btn-group">
                                        <form action="{{ route('profile.vote', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="vote" value="1">
                                            <button type="submit" class="btn btn-sm {{ $existingVote && $existingVote->vote === 1 ? 'btn-success' : 'btn-outline-success' }}">
                                                Upvote
                                            </button>
                                        </form>
                                        <form action="{{ route('profile.vote', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="vote" value="-1">
                                            <button type="submit" class="btn btn-sm {{ $existingVote && $existingVote->vote === -1 ? 'btn-danger' : 'btn-outline-danger' }}">
                                                Downvote
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Send Message Button --}}
                                <div class="mt-3">
                                    <a href="{{ route('messages.create', $user) }}" class="btn btn-primary">
                                        Send Message
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="mt-3">
                                <p class="text-muted"><a href="{{ route('login') }}" class="text-success">Login</a> to interact with this user.</p>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                {{-- Recent Topics --}}
                <div class="card bg-dark border-success mb-4">
                    <div class="card-header text-success">
                        <h5 class="mb-0">Recent Topics</h5>
                    </div>
                    <div class="card-body text-success">
                        @if($recentTopics->isEmpty())
                            <p class="text-muted">No topics yet.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach($recentTopics as $topic)
                                    <li class="mb-2">
                                        <a href="{{ route('topics.show', $topic) }}" class="text-success">{{ $topic->title }}</a>
                                        <small class="text-muted">- {{ $topic->created_at->diffForHumans() }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- Recent Posts --}}
                <div class="card bg-dark border-success">
                    <div class="card-header text-success">
                        <h5 class="mb-0">Recent Posts</h5>
                    </div>
                    <div class="card-body text-success">
                        @if($recentPosts->isEmpty())
                            <p class="text-muted">No posts yet.</p>
                        @else
                            @foreach($recentPosts as $post)
                                <div class="border-bottom border-secondary pb-2 mb-2">
                                    <a href="{{ route('topics.show', $post->topic) }}" class="text-success">{{ $post->topic->title }}</a>
                                    <small class="text-muted">- {{ $post->created_at->diffForHumans() }}</small>
                                    <p class="mb-0 mt-1 text-success">{{ Str::limit($post->content, 100) }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
