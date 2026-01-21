@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Edit Category</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (optional)</label>
                <textarea class="form-control" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Update Category</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection

