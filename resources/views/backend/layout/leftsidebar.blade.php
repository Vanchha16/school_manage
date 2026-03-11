 
<div class="d-lg-none mobile-topbar bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between">
    <button class="btn btn-dark btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="fw-semibold">Don School</div>

    <div style="width: 38px;"></div>
</div> 
 <div class="app d-flex">
      <aside id="sidebar" class="sidebar d-flex flex-column p-2">
          <div class="brand">
              <div class="brand-badge"><i class="bi bi-grid"></i></div>
              <div>
                  <div style="line-height:1;">Don <span class="text-muted">School</span></div>
                  <div class="text-muted" style="font-size:12px;font-weight:600;">
                      {{ auth()->user()->name }} • {{ ucfirst(auth()->user()->role ?? 'User') }}
                  </div>
              </div>
          </div>
          {{-- Mobile --}}
          <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title mb-0">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="p-3 border-bottom">
            <div class="fw-bold">Don School</div>
            <small class="text-muted">Admin Panel</small>
        </div>

        <ul class="nav flex-column p-2">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold text-dark' : 'text-secondary' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active fw-bold text-dark' : 'text-secondary' }}">
                    <i class="bi bi-people me-2"></i> Students
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active fw-bold text-dark' : 'text-secondary' }}">
                    <i class="bi bi-diagram-3 me-2"></i> Groups
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'active fw-bold text-dark' : 'text-secondary' }}">
                    <i class="bi bi-box-seam me-2"></i> Items
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('borrows.index') }}" class="nav-link {{ request()->routeIs('borrows.*') ? 'active fw-bold text-dark' : 'text-secondary' }}">
                    <i class="bi bi-arrow-left-right me-2"></i> Borrow
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('submissions.index') }}" class="nav-link {{ request()->routeIs('submissions.*') ? 'active fw-bold text-dark' : 'text-secondary' }}">
                    <i class="bi bi-inbox me-2"></i> Submission
                </a>
            </li>
        </ul>
    </div>
</div>
          <div class="px-2 mt-2">
              <a href="{{ route('admin.dashboard') }}" class="nav-pill @yield('dashboard_active')">
                  <i class="bi bi-speedometer2"></i> Dashboard
              </a>
              <a href="#" class="nav-pill" data-page="teachers"><i class="bi bi-person-badge"></i>Teachers</a>
              <a href="{{ url('admin/students') }}" class="nav-pill @yield('student_active')" data-page="students"><i
                      class="bi bi-people"></i>Students</a>
              <a href="{{ url('admin/items') }}" class="nav-pill @yield('item_active')"><i
                      class="bi bi-calendar-event"></i>Items</a>
              <a href="{{ url('admin/groups') }}" class="nav-pill @yield('group_active')"><i
                      class="fa-duotone fa-regular fa-user-group"></i>Groups</a>
              <a href="{{ url('admin/borrows') }}" class="nav-pill @yield('borrow_active')"><i
                      class="fa-sharp fa-regular fa-hand-holding-box"></i>Manage Items</a>
              <a href="{{ url('admin/register-student') }}" class="nav-pill @yield('register_student_active')"><i
                      class="fa-sharp fa-regular fa-hand-holding-box"></i>Register Student</a>
              <a href="{{ url('admin/submissions') }}" class="nav-pill @yield('submission_active')"><i
                      class="fa-sharp fa-regular fa-hand-holding-box"></i>Submissions</a>
              @php $role = strtolower(auth()->user()->role ?? ''); @endphp

              @if (in_array($role, ['admin', 'super admin', 'superadmin']))
                  <a href="{{ url('admin/users') }}" class="nav-pill @yield('users_active')">
                      <i class="fa-utility-fill fa-semibold fa-user"></i>Users
                  </a>
              @endif
              <a href="{{ route('borrows.late_returns') }}" class="nav-pill @yield('late_return_active')">
                  <i class="fa-sharp fa-regular fa-hand-holding-box"></i>
                  Returned Late

                  @if (($lateReturnedCount ?? 0) > 0)
                      <span class="ms-auto d-inline-flex align-items-center justify-content-center bg-danger text-white"
                          style="width:22px;height:22px;border-radius:50%;font-size:12px;font-weight:700;">
                          {{ $lateReturnedCount }}
                      </span>
                  @endif
              </a>
              <a href="{{ route('borrows.overdue') }}" class="nav-pill @yield('overdue_borrow_active')">
                  <i class="fa-sharp fa-regular fa-hand-holding-box"></i>
                  Overdue Borrow

                  @if (($overdueCount ?? 0) > 0)
                      <span class="ms-auto d-inline-flex align-items-center justify-content-center bg-danger text-white"
                          style="width:22px;height:22px;border-radius:50%;font-size:12px;font-weight:700;">
                          {{ $overdueCount }}
                      </span>
                  @endif
              </a>
          </div>

          <div class="mt-auto p-2">
              <div class="soft-card p-3">
                  <div class="d-flex align-items-center gap-2">
                      <i class="bi bi-shield-check text-success"></i>
                      <div class="fw-bold">Super Admin</div>
                  </div>
                  <div class="text-muted" style="font-size:12px;">Maths Teacher</div>
              </div>
          </div>
      </aside>
