@extends('backend.layout.master')

@section('title', 'Submission')
@section('contents')

    <div class="container-fluid py-4">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-0">Submission</h2>
                <div class="text-muted">New student registrations waiting for approval</div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <form method="GET" class="d-flex gap-2">
                    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name / phone">
                    <button class="btn btn-outline-secondary">Search</button>
                </form>

                <form method="POST" action="{{ route('submissions.cancelAll') }}"
                    onsubmit="return confirm('Are you sure you want to cancel all submissions?');">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cancel All</button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Phone</th>
                            <th>Group</th>
                            <th>Item</th>
                            <th>Image</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th style="width: 260px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $row)
                            <tr>
                                <td>{{ $row->student_name }}</td>
                                <td>{{ $row->phone_number }}</td>
                                <td>{{ $row->group->group_name ?? '-' }}</td>
                                <td>{{ $row->item->name ?? '-' }}</td>
                                <td>
                                    @if (!empty($row->item?->image))
                                        <img src="{{ asset('storage/' . $row->item->image) }}" width="60" height="60"
                                            style="object-fit:cover;border-radius:8px;">
                                    @else
                                        <span>No image</span>
                                    @endif
                                </td>
                                <td>{{ $row->qty }}</td>
                                <td>
                                    @if ($row->is_borrow_approved)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        @if ($row->is_student_existing || $row->is_student_added)
                                            <div class="text-success small">Student already in database</div>

                                            @if (!$row->is_borrow_approved)
                                                <form method="POST"
                                                    action="{{ route('submissions.approveBorrow', $row->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-primary w-100">Approve Borrow</button>
                                                </form>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('submissions.addStudent', $row->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-dark w-100">Add Student</button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('submissions.remove', $row->id) }}"
                                            onsubmit="return confirm('Remove this submission?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger w-100">Remove</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No submissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end">
                    {{ $submissions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection