@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success">Sent Messages</h2>
            <div>
                <a href="{{ route('messages.inbox') }}" class="btn btn-outline-success">Inbox</a>
                <a href="{{ route('messages.create') }}" class="btn btn-success">+ Compose</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($messages->isEmpty())
            <div class="alert alert-info">You haven't sent any messages yet.</div>
        @else
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th>To</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr>
                            <td>
                                <a href="{{ route('profile.show', $message->receiver) }}" class="text-success">
                                    {{ $message->receiver->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('messages.show', $message) }}" class="text-success text-decoration-none">
                                    {{ $message->subject }}
                                </a>
                            </td>
                            <td class="text-success">{{ $message->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <form action="{{ route('messages.destroy', $message) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
@endsection
