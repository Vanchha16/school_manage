@extends('backend.layout.master')

@section('title', 'Groups')
@section('group_active', 'active')
@section('contents')

    {{-- Full width CSS (safe override) --}}
    <style>
        /* Force full width in case layout uses fixed containers */
        .content-wrapper,
        .content,
        .container,
        .container-lg,
        .container-md,
        .container-sm {
            max-width: 100% !important;
            width: 100% !important;
        }

        /* Optional: reduce card side spacing */
        .page-full-width {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
    </style>

    <div class="container-fluid" style="padding: 3%;">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Groups</h2>
                <div class="text-secondary">Add, view, edit, and delete groups.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Search --}}
                <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="input-group" style="min-width: 320px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            class="form-control border-start-0"
                            placeholder="Search group name..."
                        >
                    </div>

                    <button class="btn btn-outline-secondary">Apply</button>
                    <a href="{{ url()->current() }}" class="btn btn-light">Reset</a>
                </form>

                {{-- Add --}}
                <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                    <i class="bi bi-people me-1"></i> Add Group
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small">
                        Total: {{ $groups->total() }}
                    </span>
                </div>

                <h5 class="fw-bold mb-3" style="display:flex; justify-content: space-between;">
                    Groups
                </h5>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:80px;">#</th>
                                <th class="text-center">Group Name</th>
                                <th class="text-center" style="width:160px;">Total Students</th>
                                <th class="text-end" style="width:120px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($groups as $k => $g)
                                <tr>
                                    <td class="text-secondary">
                                        {{ $groups->firstItem() + $k }}
                                    </td>

                                    <td class="fw-semibold text-center">
                                        {{ $g->group_name }}
                                    </td>

                                    <td class="text-center fw-semibold">
                                        {{ $g->students_count }}
                                    </td>

                                    <td class="text-end">
                                        <form
                                            action="{{ route('groups.destroy', $g->group_id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this group?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-danger py-3">
                                        No groups found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $groups->onEachSide(1)->links('vendor.pagination.adminlte-simple') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add Group Modal --}}
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label fw-semibold">
                        Group Name <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="group_name"
                        class="form-control"
                        placeholder="e.g., Grade 10 / Class A"
                        required
                    >
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
@endsection