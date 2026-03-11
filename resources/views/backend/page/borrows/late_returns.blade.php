@extends('backend.layout.master')

@section('title', 'Returned Late')
@section('late_return_active', 'active')
@section('contents')

    <style>
        .content-wrapper,
        .content,
        .container,
        .container-lg,
        .container-md,
        .container-sm {
            max-width: 100% !important;
            width: 100% !important;
        }
    </style>

    <div class="container-fluid" style="padding:3%;">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Returned Late</h2>
                <div class="text-secondary">Students who returned items late (more than 3 days).</div>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group" style="min-width: 320px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                        placeholder="Search student or item...">
                </div>

                <button class="btn btn-outline-secondary">Apply</button>
                <a href="{{ url()->current() }}" class="btn btn-light">Reset</a>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 w-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small">
                        Total Late Returns: {{ $lateReturns->total() }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:80px;">#</th>
                                <th>Student</th>
                                <th>Item</th>
                                <th class="text-center" style="width:160px;">Borrow Date</th>
                                <th class="text-center" style="width:160px;">Return Date</th>
                                <th class="text-center" style="width:150px;">Total Days</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($lateReturns as $k => $b)
                                @php
                                    $totalDays = 0;
                                    $lateDays = 0;

                                    if (!empty($b->borrow_date) && !empty($b->return_date)) {
                                        $borrow = \Carbon\Carbon::parse($b->borrow_date);
                                        $ret = \Carbon\Carbon::parse($b->return_date);

                                        $hours = $borrow->diffInHours($ret);
                                        $totalDays = (int) ceil($hours / 24);

                                        $lateDays = (int) ceil(max(0, $hours - 72) / 24); // after 3 days
                                    }
                                @endphp

                                <tr>
                                    <td class="text-secondary">{{ $lateReturns->firstItem() + $k }}</td>

                                    <td class="fw-semibold">{{ $b->student->student_name ?? '—' }}</td>

                                    <td>{{ $b->item->name ?? '—' }}</td>

                                    <td class="text-center">
                                        {{ $b->borrow_date ? \Carbon\Carbon::parse($b->borrow_date)->format('d M Y H:i') : '—' }}
                                    </td>


                                    <td class="text-center">
                                        {{ $b->return_date ? \Carbon\Carbon::parse($b->return_date)->format('d M Y H:i') : '—' }}
                                    </td>

                                    <td class="text-center">{{ $totalDays }} days</td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-danger py-3">
                                        No late returns found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $lateReturns->onEachSide(1)->links('vendor.pagination.adminlte-simple') }}
                </div>

            </div>
        </div>
    </div>
@endsection
