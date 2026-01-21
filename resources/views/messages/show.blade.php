@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Message</h2>
            <a href="{{ route('messages.inbox') }}" class="btn btn-outline-success">Back to Inbox</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card bg-dark border-success">
            <div class="card-header text-success d-flex justify-content-between align-items-center">
                <div>
                    <strong>From:</strong>
                    <a href="{{ route('profile.show', $message->sender) }}" class="text-success">{{ $message->sender->name }}</a>
                    <span class="mx-2">→</span>
                    <strong>To:</strong>
                    <a href="{{ route('profile.show', $message->receiver) }}" class="text-success">{{ $message->receiver->name }}</a>
                </div>
                <small class="text-muted">{{ $message->created_at->format('M d, Y H:i') }}</small>
            </div>
            <div class="card-body text-success">
                <h5 class="card-title">{{ $message->subject }}</h5>
                <hr class="border-success">
                <div class="card-text">{!! \App\Helpers\ContentRenderer::render($message->content) !!}</div>

                {{-- Display Attachments --}}
                @if($message->attachments->count() > 0)
                    <div class="attachments mt-3 pt-3 border-top border-secondary">
                        <small class="text-muted d-block mb-2">Attachments:</small>
                        <div class="row g-2">
                            @foreach($message->attachments as $attachment)
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
            <div class="card-footer">
                @if(Auth::id() === $message->receiver_id)
                    <a href="{{ route('messages.reply', $message) }}" class="btn btn-primary">Reply</a>
                @endif
                <form action="{{ route('messages.destroy', $message) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" onclick="return confirm('Delete this message?')">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
