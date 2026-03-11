@extends('backend.layout.master')

@section('title', 'Borrows')
@section('borrow_active', 'active')

@section('contents')
    <div class="container-fluid">

        {{-- ✅ SHOW ERRORS + SUCCESS HERE --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <h2 class="mb-1">Borrows</h2>
                <p class="text-muted mb-0">Borrow and return items from IT room.</p>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-dark d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#borrowModal">
                    <span class="fs-5">+</span> Borrow Item
                </button>

                <button class="btn btn-outline-dark d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#returnModal">
                    <i class="bi bi-box-arrow-in-down"></i> Return Item
                </button>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">Total Borrows (Records)</small>
                        <h3 class="mb-0">{{ $stats['total_records'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">Active (Borrowed Records)</small>
                        <h3 class="mb-0">{{ $stats['active_records'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">Returned (Records)</small>
                        <h3 class="mb-0">{{ $stats['returned_records'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                {{-- Search --}}
                <div class="justify-content-end mb-3">
                    <form method="GET" action="{{ route('borrows.index') }}" class="row g-2 align-items-end mb-3">

                        {{-- Search by student name --}}
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Search Student</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Type student name...">
                            </div>
                        </div>

                        {{-- Status filter --}}
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="BORROWED" {{ request('status') == 'BORROWED' ? 'selected' : '' }}>BORROWED
                                </option>
                                <option value="RETURNED" {{ request('status') == 'RETURNED' ? 'selected' : '' }}>RETURNED
                                </option>
                                <option value="OVERDUE" {{ request('status') == 'OVERDUE' ? 'selected' : '' }}>OVERDUE
                                </option>
                            </select>
                        </div>

                        {{-- Group filter --}}
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Group</label>
                            <select name="group_id" class="form-select">
                                <option value="">All</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->group_id }}"
                                        {{ (string) request('group_id') === (string) $g->group_id ? 'selected' : '' }}>
                                        {{ $g->group_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Item filter --}}
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Item</label>
                            <select name="item_id" class="form-select">
                                <option value="">All</option>
                                @foreach ($items as $it)
                                    <option value="{{ $it->Itemid }}"
                                        {{ (string) request('item_id') === (string) $it->Itemid ? 'selected' : '' }}>
                                        {{ $it->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-outline-secondary">Apply</button>
                            <a href="{{ route('borrows.index') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="border-bottom">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Student</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Borrow Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th class="text-end" style="width:200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($borrows ?? [] as $i => $borrow)
                                <tr class="border-bottom">
                                    <td>{{ $i + 1 }}</td>

                                    <td>
                                        <div class="fw-semibold">{{ $borrow->student->student_name ?? 'N/A' }}</div>
                                        <small class="text-muted">
                                            {{ $borrow->student->student_id ?? '' }}
                                            @if (!empty($borrow->student?->group?->group_name))
                                                • {{ $borrow->student->group->group_name }}
                                            @endif
                                        </small>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $borrow->item->name ?? 'N/A' }}</div>
                                    </td>

                                    <td>{{ $borrow->qty ?? 1 }}</td>

                                    <td>{{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td>{{ $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date)->format('d M Y H:i') : '-' }}
                                    </td>

                                    <td>
                                        @php
                                            $st = $borrow->status ?? 'BORROWED';
                                            $cls = match ($st) {
                                                'RETURNED' => 'success',
                                                'BORROWED' => 'warning',
                                                'OVERDUE' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge rounded-pill bg-{{ $cls }} px-3 py-2">
                                            {{ ucfirst(strtolower($st)) }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        {{-- View Detail --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                            data-student="{{ $borrow->student->student_name ?? 'N/A' }}"
                                            data-item="{{ $borrow->item->name ?? 'N/A' }}"
                                            data-qty="{{ $borrow->qty ?? 1 }}"
                                            data-status="{{ $borrow->status ?? 'BORROWED' }}"
                                            data-borrow-date="{{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->timezone('Asia/Jakarta')->format('d M Y H:i') : '' }}"
                                            data-due-date="{{ $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date)->format('d M Y') : '' }}"
                                            data-return-date="{{ $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date)->timezone('Asia/Jakarta')->format('d M Y H:i') : '' }}"
                                            data-notes="{{ $borrow->notes ?? '' }}"
                                            data-return-notes="{{ $borrow->return_notes ?? '' }}"
                                            data-condition="{{ $borrow->condition ?? '' }}">
                                            View
                                        </button>

                                        @php
                                            $status = $borrow->status ?? 'BORROWED';
                                        @endphp

                                        @if (in_array($status, ['BORROWED', 'OVERDUE']))
                                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal"
                                                data-bs-target="#returnModal" data-borrow-id="{{ $borrow->id }}">
                                                Return
                                            </button>

                                            <form action="{{ route('borrows.destroy', $borrow->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this borrow record?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            {{-- Returned --}}
                                            <form action="{{ route('borrows.undoReturn', $borrow->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    onclick="return confirm('Undo return? This will set status back to BORROWED.')">
                                                    Edit / Undo
                                                </button>
                                            </form>
                                        @endif
                                       
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- ✅ FIXED colspan from 7 -> 8 --}}
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No borrow records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- pagination (if needed) --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $borrows->links() }}
                    </div>
                    {{-- {{ $borrows->links() }} --}}
                </div>

            </div>
        </div>
    </div>

    {{-- ===================== BORROW MODAL ===================== --}}
    <div class="modal fade" id="borrowModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Borrow Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('borrows.borrow') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Student Autocomplete --}}
                            <div class="col-md-6">
                                <label class="form-label">Student <span class="text-danger">*</span></label>

                                <input type="text" id="student_search" class="form-control" list="students_list"
                                    placeholder="Type student name..." autocomplete="off" required>

                                <datalist id="students_list">
                                    @foreach ($students as $s)
                                        <option value="{{ $s->student_name }}" data-id="{{ $s->student_id }}"></option>
                                    @endforeach
                                </datalist>

                                {{-- Hidden student_id that will be submitted --}}
                                <input type="hidden" name="student_id" id="student_id" required>
                                <small class="text-muted">Start typing, then select from suggestions.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Item <span class="text-danger">*</span></label>
                                <select name="item_id" class="form-select" required>
                                    <option value="">-- Select Item --</option>
                                    @foreach ($items as $it)
                                        <option value="{{ $it->Itemid }}">{{ $it->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Due Date <small class="text-muted">(optional)</small></label>
                                <input type="date" name="due_date" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Qty <span class="text-danger">*</span></label>
                                <input type="number" name="qty" class="form-control" min="1" value="1"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="BORROWED" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Example: Borrow for class presentation"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== RETURN MODAL ===================== --}}
    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Return Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('borrows.return') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Select Borrow Record <span class="text-danger">*</span></label>
                                <select name="borrow_id" id="return_borrow_id" class="form-select" required>
                                    <option value="">-- Select active borrow --</option>
                                    @foreach ($activeBorrows as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->student->student_name ?? 'N/A' }} - {{ $b->item->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Only active (BORROWED) records shown.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Return Date</label>
                                <input type="datetime-local" name="return_date" class="form-control"
                                    value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Item Condition</label>
                                <select name="condition" class="form-select" required>
                                    <option value="Good">Good</option>
                                    <option value="Slightly Damaged">Slightly Damaged</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="Lost">Lost</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Return Notes</label>
                                <textarea name="return_notes" class="form-control" rows="3"
                                    placeholder="Example: Charger cable slightly damaged"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Save Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== DETAIL MODAL ===================== --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Borrow Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Student</div>
                            <div class="fw-semibold" id="d_student">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Item</div>
                            <div class="fw-semibold" id="d_item">-</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Qty</div>
                            <div class="fw-semibold" id="d_qty">-</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Status</div>
                            <div class="fw-semibold" id="d_status">-</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Condition</div>
                            <div class="fw-semibold" id="d_condition">-</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Borrow Date/Time</div>
                            <div class="fw-semibold" id="d_borrow_date">-</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Due Date</div>
                            <div class="fw-semibold" id="d_due_date">-</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Return Date/Time</div>
                            <div class="fw-semibold" id="d_return_date">-</div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small">Notes</div>
                            <div class="border rounded p-2" id="d_notes">-</div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small">Return Notes</div>
                            <div class="border rounded p-2" id="d_return_notes">-</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

{{-- ✅ JS: auto select borrow id in Return modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const returnModal = document.getElementById('returnModal');
        const returnSelect = document.getElementById('return_borrow_id');

        if (!returnModal || !returnSelect) return;

        returnModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const borrowId = button?.getAttribute('data-borrow-id');

            if (borrowId) {
                returnSelect.value = borrowId;
            } else {
                returnSelect.value = '';
            }
        });
    });
</script>

{{-- ✅ JS: fill detail modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = document.getElementById('detailModal');

        if (!detailModal) return;

        detailModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;

            document.getElementById('d_student').textContent = btn.getAttribute('data-student') || '-';
            document.getElementById('d_item').textContent = btn.getAttribute('data-item') || '-';
            document.getElementById('d_qty').textContent = btn.getAttribute('data-qty') || '1';
            document.getElementById('d_status').textContent = btn.getAttribute('data-status') || '-';
            document.getElementById('d_condition').textContent = btn.getAttribute('data-condition') || '-';

            document.getElementById('d_borrow_date').textContent = btn.getAttribute('data-borrow-date') || '-';
            document.getElementById('d_due_date').textContent = btn.getAttribute('data-due-date') || '-';
            document.getElementById('d_return_date').textContent = btn.getAttribute('data-return-date') || '-';

            document.getElementById('d_notes').textContent = btn.getAttribute('data-notes') || '-';
            document.getElementById('d_return_notes').textContent = btn.getAttribute('data-return-notes') || '-';
        });
    });
</script>

{{-- ✅ JS: student datalist -> hidden student_id --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('student_search');
        const hidden = document.getElementById('student_id');
        const list = document.getElementById('students_list');

        if (!input || !hidden || !list) return;

        input.addEventListener('input', function() {
            const value = input.value.trim();
            hidden.value = '';

            const options = list.querySelectorAll('option');
            for (const opt of options) {
                if (opt.value === value) {
                    hidden.value = opt.dataset.id;
                    break;
                }
            }
        });

        input.addEventListener('blur', function() {
            if (!hidden.value) {
                input.setCustomValidity('Please select a student from the list.');
            } else {
                input.setCustomValidity('');
            }
        });
    });
</script>

{{-- ✅ JS: open Borrow modal from submissions (openBorrow=1) + fill student + focus item + clean URL --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const url = new URL(window.location.href);
        const params = url.searchParams;

        if (params.get('openBorrow') === '1') {
            const studentName = params.get('student_name') || '';
            const studentId   = params.get('student_id') || '';

            // Fill student input + hidden id
            const input  = document.getElementById('student_search');
            const hidden = document.getElementById('student_id');

            if (input) input.value = studentName;
            if (hidden) hidden.value = studentId;

            // Open Borrow modal
            const modalEl = document.getElementById('borrowModal');
            if (modalEl && window.bootstrap) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                modalEl.addEventListener('shown.bs.modal', function () {
                    // focus item dropdown
                    const itemSelect = modalEl.querySelector('select[name="item_id"]');
                    if (itemSelect) itemSelect.focus();
                }, { once: true });
            }

            // Clean URL (remove params so it doesn't reopen on refresh)
            params.delete('openBorrow');
            params.delete('student_id');
            params.delete('student_name');

            window.history.replaceState(
                {},
                document.title,
                url.pathname + (params.toString() ? '?' + params.toString() : '')
            );
        }
    });
</script>
@endsection
