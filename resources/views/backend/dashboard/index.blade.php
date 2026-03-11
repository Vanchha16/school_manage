@extends('backend.layout.master')

@section('title', 'School Dashboard')
@section('das_active', 'active')

@section('contents')
    <div class="container-fluid py-3 py-lg-4">

        <!-- Topbar -->
        <div class="topbar d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
                <button id="btnMenu" class="btn btn-light d-lg-none" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
                <h3 class="m-0">Dashboard</h3>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="searchbar d-none d-md-flex align-items-center gap-2 border rounded px-2 py-1 bg-white">
                    <i class="bi bi-search text-muted"></i>
                    <input id="searchInput" type="text" placeholder="Search..." class="border-0 outline-0" />
                </div>

                <button class="btn btn-light" id="btnBell" aria-label="Notifications">
                    <i class="bi bi-bell"></i>
                </button>
                <button class="btn btn-light" id="btnSettings" aria-label="Settings">
                    <i class="bi bi-gear"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="rounded-circle bg-secondary-subtle d-inline-grid"
                            style="width:26px;height:26px;place-items:center;">
                            <i class="bi bi-person"></i>
                        </span>
                        <span class="d-none d-md-inline fw-semibold">{{ auth()->user()->name ?? 'User' }}</span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" id="actionProfile">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tabs row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-dark btn-sm tablink active" data-tab="overview">Overview</button>
                <button class="btn btn-outline-dark btn-sm tablink" data-tab="unpaid">Unpaid Students</button>
                <button class="btn btn-outline-dark btn-sm tablink" data-tab="expense">School Expense</button>
                <button class="btn btn-outline-dark btn-sm tablink" data-tab="recent">Recent Students</button>
                <button class="btn btn-outline-dark btn-sm tablink" data-tab="courses">Courses</button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i></div>
                <input id="dateStart" type="date" class="form-control form-control-sm" style="max-width:160px;">
                <span class="text-muted small">to</span>
                <input id="dateEnd" type="date" class="form-control form-control-sm" style="max-width:160px;">
                <button class="btn btn-sm btn-dark" id="applyRange">Apply</button>
            </div>
        </div>

        <!-- Tab contents -->
        <div id="tabContent">
            <!-- Overview -->
            <section class="tabpane" data-tabpane="overview">
                <div class="row g-3">
                    <!-- Metrics + Chart -->
                    <div class="col-12 col-xl-8">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="text-muted small">Total Earning</div>
                                        <div class="fs-4 fw-bold">$<span id="earnValue">58,123.00</span></div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="text-muted small">This Month</div>
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="bi bi-arrow-up"></i> <span id="earnDelta">45.03%</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="text-muted small">Total Expenses</div>
                                        <div class="fs-4 fw-bold">$<span id="expValue">18,201.00</span></div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="text-muted small">This Month</div>
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="bi bi-arrow-down"></i> <span id="expDelta">12.10%</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-bold">Earnings</div>
                                            <div class="text-muted small" id="rangeLabel">1 Jan, 2023 - 3 Nov, 2023</div>
                                        </div>
                                        <div style="height:320px;">
                                            <canvas id="earningsChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="col-12 col-xl-4">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="fw-bold">Student Activity</div>
                                    <button class="btn btn-sm btn-light" id="filterActivity" title="Filter">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>

                                <div id="activityList" class="small text-muted">
                                    <!-- JS render -->
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="fw-bold mb-3">Attendance</div>

                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:160px; height:160px;">
                                        <canvas id="attendanceChart"></canvas>
                                    </div>
                                    <div class="ms-1 w-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="d-inline-block rounded-2"
                                                style="width:10px;height:10px;background:#ef4444;"></span>
                                            <div class="text-muted">Absent</div>
                                            <div class="ms-auto fw-bold" id="absentPct">35%</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-block rounded-2"
                                                style="width:10px;height:10px;background:#22c55e;"></span>
                                            <div class="text-muted">Present</div>
                                            <div class="ms-auto fw-bold" id="presentPct">65%</div>
                                        </div>

                                        <button class="btn btn-sm btn-dark mt-3" id="randomizeAttendance">
                                            Randomize
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Other tabs -->
            <section class="tabpane d-none" data-tabpane="unpaid">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">Unpaid Students</h5>
                        <p class="text-muted mb-0">Hook this up to your data table / API.</p>
                    </div>
                </div>
            </section>

            <section class="tabpane d-none" data-tabpane="expense">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">School Expense</h5>
                        <p class="text-muted mb-0">Add expense reports, filters, and export actions here.</p>
                    </div>
                </div>
            </section>

            <section class="tabpane d-none" data-tabpane="recent">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">Recent Students</h5>
                        <p class="text-muted mb-0">Show recent enrollments / admissions.</p>
                    </div>
                </div>
            </section>

            <section class="tabpane d-none" data-tabpane="courses">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">Courses</h5>
                        <p class="text-muted mb-0">Add course cards or a course list view.</p>
                    </div>
                </div>
            </section>
        </div>

    </div>

    {{-- Chart.js + page JS (NO VITE) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Tabs
        document.querySelectorAll('.tablink').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tablink').forEach(b => b.classList.remove('active',
                    'btn-dark'));
                document.querySelectorAll('.tablink').forEach(b => b.classList.add('btn-outline-dark'));

                btn.classList.add('active', 'btn-dark');
                btn.classList.remove('btn-outline-dark');

                const tab = btn.dataset.tab;
                document.querySelectorAll('.tabpane').forEach(p => p.classList.add('d-none'));
                document.querySelector(`[data-tabpane="${tab}"]`)?.classList.remove('d-none');
            });
        });

        // Activity list demo
        const activities = [{
                text: 'New student registered',
                time: '2 mins ago'
            },
            {
                text: 'Borrow item created',
                time: '10 mins ago'
            },
            {
                text: 'Returned item saved',
                time: '1 hour ago'
            },
        ];
        document.getElementById('activityList').innerHTML = activities.map(a =>
            `<div class="d-flex justify-content-between border-bottom py-2">
                <div>${a.text}</div>
                <div class="text-muted">${a.time}</div>
            </div>`
        ).join('');

        // Earnings chart demo
        const earningsCtx = document.getElementById('earningsChart');
        if (earningsCtx) {
            new Chart(earningsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Earnings',
                        data: [1200, 1900, 1500, 2200, 2500, 3000],
                        tension: 0.3
                    }]
                }
            });
        }

        // Attendance chart demo
        const attCtx = document.getElementById('attendanceChart');
        let attendanceChart = null;

        function renderAttendance(absent, present) {
            document.getElementById('absentPct').textContent = absent + '%';
            document.getElementById('presentPct').textContent = present + '%';

            if (!attCtx) return;
            if (attendanceChart) attendanceChart.destroy();

            attendanceChart = new Chart(attCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Absent', 'Present'],
                    datasets: [{
                        data: [absent, present]
                    }]
                }
            });
        }

        renderAttendance(35, 65);

        document.getElementById('randomizeAttendance')?.addEventListener('click', () => {
            const absent = Math.floor(Math.random() * 60) + 10;
            const present = 100 - absent;
            renderAttendance(absent, present);
        });
    </script>
@endsection
