@extends('backend.layout.master')

@section('title', 'Overdue Borrow')
@section('overdue_borrow_active', 'active')
@section('contents')

<style>
    .content-wrapper,.content,.container,.container-lg,.container-md,.container-sm{
        max-width:100%!important;width:100%!important;
    }
</style>

<div class="container-fluid" style="padding:3%;">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Overdue Borrow</h2>
            <div class="text-secondary">Borrowed more than 3 days and still not returned.</div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center">
            <div class="input-group" style="min-width: 320px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control border-start-0"
                       placeholder="Search student / phone / item...">
            </div>
            <button class="btn btn-outline-secondary">Apply</button>
            <a href="{{ url()->current() }}" class="btn btn-light">Reset</a>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 w-100">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle mb-0 w-100">
                    <thead>
                        <tr class="text-secondary small">
                            <th style="width:80px;">#</th>
                            <th>Student</th>
                            <th style="width:160px;">Phone</th>
                            <th>Item</th>
                            <th class="text-center" style="width:160px;">Borrow Date</th>
                            <th class="text-center" style="width:120px;">Late Days</th>
                            <th class="text-center" style="width:120px;">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($overdues as $k => $b)
                            @php
                                $lateDays = \Carbon\Carbon::parse($b->borrow_date)->diffInDays(now());
                            @endphp

                            <tr>
                                <td class="text-secondary">{{ $overdues->firstItem() + $k }}</td>

                                <td class="fw-semibold">
                                    {{ $b->student->student_name ?? '—' }}
                                </td>

                                <td>
                                    {{ $b->student->phone_number ?? '—' }}
                                </td>

                                <td>{{ $b->item->Itemname ?? $b->item->item_name ?? $b->item->name ?? '—' }}</td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($b->borrow_date)->format('d M Y H:i') }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        {{ $lateDays }} days
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge rounded-pill bg-warning text-dark">Borrowed</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-danger py-3">
                                    No overdue borrows
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                {{ $overdues->onEachSide(1)->links('vendor.pagination.adminlte-simple') }}
            </div>

        </div>
    </div>
</div>
@endsection