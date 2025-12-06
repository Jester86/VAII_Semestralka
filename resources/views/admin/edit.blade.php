@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Edit User</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select class="form-select" name="role">
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Reputation</label>
                <input type="number" class="form-control" name="reputation" value="{{ old('reputation', $user->reputation) }}" min="0">
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
