@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Edit Topic</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('topics.update', $topic) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Topic Title</label>
                <input type="text" class="form-control" name="title" value="{{ old('title', $topic->title) }}" required>
            </div>

            <button type="submit" class="btn btn-success">Update Topic</button>
            <a href="{{ route('topics.show', $topic) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection

