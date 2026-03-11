@extends('backend.layout.master')

@section('title', 'Students')
@section('student_active', 'active')

@section('contents')
    @include('backend.partials.alert')

    <div class="container-fluid py-4">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Students</h2>
                <div class="text-secondary">Add, view, edit, and delete students.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="GET" action="{{ url()->current() }}" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                            placeholder="Search name, phone, group, status..." style="min-width: 320px;">
                    </div>
                </form>

                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Student
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Total Students</div>
                        <div class="fs-3 fw-bold">{{ $statTotal ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Active</div>
                        <div class="fs-3 fw-bold">{{ $statActive ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Inactive</div>
                        <div class="fs-3 fw-bold">{{ $statInactive ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:60px;">#</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th>Phone Number</th>
                                <th>Group</th>
                                <th>Status</th>
                                <th class="text-end" style="width:180px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($students as $key => $s)
                                <tr>
                                    <td class="text-secondary">
                                        {{ ($students->currentPage() - 1) * $students->perPage() + $key + 1 }}
                                    </td>

                                    <td class="fw-semibold">{{ $s->student_name }}</td>
                                    <td>{{ $s->gender ?: '-' }}</td>
                                    <td>{{ $s->phone_number ?: '-' }}</td>
                                    <td>{{ $s->group?->group_name ?: '-' }}</td>

                                    <td>
                                        @if ($s->status == 1)
                                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#viewStudentModal{{ $s->student_id }}" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editStudentModal{{ $s->student_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="{{ route('students.destroy', ['student_id' => $s->student_id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete this student?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-danger py-4">No Data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $students->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

    </div>

    {{-- Add Student Modal --}}
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('students.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="student_name" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="">-- Select Gender --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Group</label>
                            <select name="group_id" class="form-select" required>
                                <option value="">-- Select Group --</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->group_id }}">{{ $g->group_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3 py-2" required>
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
    @foreach ($students as $s)
        <div class="modal fade" id="editStudentModal{{ $s->student_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST"
                    action="{{ route('students.update', ['student_id' => $s->student_id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Student Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="student_name" class="form-control"
                                    value="{{ old('student_name', $s->student_name) }}" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male" {{ old('gender', $s->gender) == 'Male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="Female" {{ old('gender', $s->gender) == 'Female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control"
                                    value="{{ old('phone_number', $s->phone_number) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Group</label>
                                <select name="group_id" class="form-select" required>
                                    <option value="">-- Select Group --</option>
                                    @foreach ($groups as $g)
                                        <option value="{{ $g->group_id }}"
                                            {{ (string) old('group_id', $s->group_id) === (string) $g->group_id ? 'selected' : '' }}>
                                            {{ $g->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select rounded-3 py-2" required>
                                    <option value="1" {{ old('status', $s->status) == 1 ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ old('status', $s->status) == 0 ? 'selected' : '' }}>Inactive
                                    </option>
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

    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('groups.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
                    <input type="text" name="group_name" class="form-control" placeholder="e.g., Grade 10 / Class A"
                        required>
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
    @foreach ($students as $s)
    <div class="modal fade" id="viewStudentModal{{ $s->student_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Student Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">

                        <div class="col-12">
                            <div class="p-3 rounded-4 bg-light border">
                                <h4 class="fw-bold mb-1">{{ $s->student_name }}</h4>
                                <div class="text-secondary">
                                    {{ $s->group?->group_name ?: '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Student Name</label>
                            <div class="fw-semibold">{{ $s->student_name ?: '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Gender</label>
                            <div class="fw-semibold">{{ $s->gender ?: '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Phone Number</label>
                            <div class="fw-semibold">{{ $s->phone_number ?: '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Group</label>
                            <div class="fw-semibold">{{ $s->group?->group_name ?: '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Status</label>
                            <div>
                                @if ($s->status == 1)
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                        Active
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Created At</label>
                            <div class="fw-semibold">
                                {{ $s->created_at ? $s->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="text-secondary small mb-1 d-block">Updated At</label>
                            <div class="fw-semibold">
                                {{ $s->updated_at ? $s->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endforeach
@endsection
