@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Admin Panel</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif


        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Reputation</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->reputation }}</td>
                    <td>
                        {{-- Edit Button --}}
                        <a href="{{ route('admin.edit', $user) }}" class="btn btn-sm btn-primary">Edit</a>

                        {{-- Reset Password Button --}}
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}">
                            Reset Password
                        </button>

                        {{-- Delete Form --}}
                        <form action="{{ route('admin.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>

                {{-- Reset Password Modal --}}
                <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-labelledby="resetPasswordLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('admin.reset-password', $user) }}">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="resetPasswordLabel{{ $user->id }}">Reset Password for {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-warning">Reset Password</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            @endforeach
            </tbody>
        </table>
    </div>
@endsection
