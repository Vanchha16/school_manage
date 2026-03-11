@extends('backend.layout.master')

@section('title', 'Users')
@section('user_active', 'active')

@section('contents')
    <div class="container-fluid py-4">

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success rounded-4">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Users</h2>
                <div class="text-secondary">Manage system users (admin accounts).</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Search --}}
                <form method="GET" action="{{ url()->current() }}" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                            placeholder="Search name or email..." style="min-width: 320px;">
                    </div>
                    <button class="btn btn-outline-secondary ms-2">Search</button>
                </form>

                {{-- Add --}}
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg me-1"></i> Add User
                </button>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Total Users</div>
                        <div class="fs-3 fw-bold">{{ $statTotal ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:60px;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end" style="width:180px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $key => $u)
                                <tr>
                                    <td class="text-secondary">{{ $users->firstItem() + $key }}</td>
                                    <td class="fw-semibold">{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->role ?? 'Admin' }}</td>
                                    <td>
                                        @if (($u->status ?? 1) == 1)
                                            <span
                                                class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Active</span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        {{-- Edit --}}
                                        <button class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $u->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete this user?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-danger py-4">No Users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $users->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

    </div>

    {{-- Add User Modal --}}

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ old('role', 'admin') == 'admin' ? 'selected' : '' }}>Admin
                                </option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="student"{{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-dark">
                        <i class="bi bi-check2-circle me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach ($users as $u)
        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('users.update', $u->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $u->name }}"
                                    required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ $u->email }}"
                                    required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">New Password <small
                                        class="text-muted">(optional)</small></label>
                                <input type="password" name="password" class="form-control" minlength="6">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="admin" {{ old('role', $u->role) == 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                    <option value="staff" {{ old('role', $u->role) == 'staff' ? 'selected' : '' }}>Staff
                                    </option>
                                    <option value="student"{{ old('role', $u->role) == 'student' ? 'selected' : '' }}>
                                        Student</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ old('status', $u->status ?? 1) == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ old('status', $u->status ?? 1) == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-dark">
                            <i class="bi bi-check2-circle me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

@endsection
