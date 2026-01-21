@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success">Admin Panel</h2>
            <span class="badge bg-success fs-6">{{ $users->count() }} Users</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any() && !session('reset_password_user_id'))
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="card bg-dark border-success">
            <div class="card-header text-success d-flex justify-content-between align-items-center">
                <span><strong>User Management</strong></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr class="border-success">
                                <th class="text-success d-none d-md-table-cell">ID</th>
                                <th class="text-success">Name</th>
                                <th class="text-success d-none d-lg-table-cell">Email</th>
                                <th class="text-success d-none d-sm-table-cell">Role</th>
                                <th class="text-success d-none d-sm-table-cell">Rep</th>
                                <th class="text-success">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-secondary">
                                    <td class="text-success d-none d-md-table-cell">{{ $user->id }}</td>
                                    <td>
                                        <a href="{{ route('profile.show', $user) }}" class="text-success text-decoration-none fw-bold">
                                            {{ $user->name }}
                                        </a>
                                        {{-- Show role badge under name on mobile --}}
                                        <div class="d-sm-none mt-1">
                                            @if($user->role === 'admin')
                                                <span class="badge bg-warning text-dark">{{ ucfirst($user->role) }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                            @endif
                                            <span class="badge {{ $user->reputation >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $user->reputation }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-success d-none d-lg-table-cell">{{ $user->email }}</td>
                                    <td class="d-none d-sm-table-cell">
                                        @if($user->role === 'admin')
                                            <span class="badge bg-warning text-dark">{{ ucfirst($user->role) }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge {{ $user->reputation >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user->reputation }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column flex-xl-row gap-1">
                                            <a href="{{ route('admin.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit User">
                                                <span class="d-none d-md-inline">Edit</span>
                                                <i class="d-md-none">✏️</i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}" title="Reset Password">
                                                <span class="d-none d-md-inline">Password</span>
                                                <i class="d-md-none">🔑</i>
                                            </button>
                                            <form action="{{ route('admin.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete User">
                                                    <span class="d-none d-md-inline">Delete</span>
                                                    <i class="d-md-none">🗑️</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="row mt-4 g-3">
            <div class="col-6 col-md-4">
                <div class="card bg-dark border-success text-center">
                    <div class="card-body">
                        <h3 class="text-success">{{ $users->where('role', 'admin')->count() }}</h3>
                        <p class="text-success mb-0">Admins</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card bg-dark border-success text-center">
                    <div class="card-body">
                        <h3 class="text-success">{{ $users->where('role', 'user')->count() }}</h3>
                        <p class="text-success mb-0">Regular Users</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card bg-dark border-success text-center">
                    <div class="card-body">
                        <h3 class="text-success">{{ $users->sum('reputation') }}</h3>
                        <p class="text-success mb-0">Total Reputation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    {{-- Reset Password Modals --}}
    @foreach($users as $user)
        <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-labelledby="resetPasswordLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.reset-password', $user) }}">
                    @csrf
                    <div class="modal-content bg-dark border-success">
                        <div class="modal-header border-success">
                            <h5 class="modal-title text-success" id="resetPasswordLabel{{ $user->id }}">
                                Reset Password for {{ $user->name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any() && session('reset_password_user_id') == $user->id)
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label text-success">New Password</label>
                                <input type="password" class="form-control bg-dark text-success border-success" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-success">Confirm Password</label>
                                <input type="password" class="form-control bg-dark text-success border-success" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer border-success">
                            <button type="submit" class="btn btn-warning">Reset Password</button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @if(session('reset_password_user_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalId = 'resetPasswordModal{{ session('reset_password_user_id') }}';
                var modalElement = document.getElementById(modalId);
                if (modalElement) {
                    var modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });
        </script>
    @endif
@endsection
