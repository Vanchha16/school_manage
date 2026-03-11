
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SimpleLoginController;

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\BorrowController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\GroupController;
use App\Http\Controllers\Backend\StudentController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\StudentRegisterController;
use App\Http\Controllers\Backend\SubmissionController;
/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('student.register');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [SimpleLoginController::class, 'show'])->name('login');
Route::post('/login', [SimpleLoginController::class, 'login'])->name('login.post');
Route::post('/logout', [SimpleLoginController::class, 'logout'])->name('logout');
Route::get('admin/late-returns', [\App\Http\Controllers\Backend\BorrowController::class, 'lateReturns'])
    ->name('borrows.late_returns');
Route::get('admin/overdue-borrows', [\App\Http\Controllers\Backend\BorrowController::class, 'overdueBorrows'])
->name('borrows.overdue');
/*
|--------------------------------------------------------------------------
| DASHBOARD (after login)
|--------------------------------------------------------------------------
| admin/staff -> admin.dashboard
| student     -> student.register
*/
Route::middleware(['auth'])->get('/dashboard', function () {
    $role = strtolower(auth()->user()->role ?? 'student');

    if (in_array($role, ['admin', 'staff'])) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('student.register');
})->name('dashboard');

Route::prefix('admin')->group(function () {
    Route::get('submissions', [\App\Http\Controllers\Backend\SubmissionController::class, 'index'])->name('submissions.index');
    Route::post('submissions', [\App\Http\Controllers\Backend\SubmissionController::class, 'store'])->name('submissions.store');

    Route::post('submissions/{submission}/approve', [\App\Http\Controllers\Backend\SubmissionController::class, 'approve'])->name('submissions.approve');
    Route::delete('submissions/{submission}', [\App\Http\Controllers\Backend\SubmissionController::class, 'destroy'])->name('submissions.destroy');
});
Route::post('admin/submissions/{submission}/go-manage', [SubmissionController::class, 'goManage'])
    ->name('submissions.go_manage');
/*
|--------------------------------------------------------------------------
| STUDENT ONLY
|--------------------------------------------------------------------------
*/

Route::get('/register-student', [StudentRegisterController::class, 'create'])->name('student.register');
Route::post('/register-student', [StudentRegisterController::class, 'store'])->name('student.register.store');


/*
|--------------------------------------------------------------------------
| ADMIN + STAFF (Management)
|--------------------------------------------------------------------------
| ✅ admin can do everything because admin is included here
*/
Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Borrows
    Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
    Route::post('/borrows/borrow', [BorrowController::class, 'storeBorrow'])->name('borrows.borrow');
    Route::post('/borrows/return', [BorrowController::class, 'storeReturn'])->name('borrows.return');
    Route::post('/borrows/{borrow}/undo-return', [BorrowController::class, 'undoReturn'])->name('borrows.undoReturn');
    Route::delete('/borrows/{borrow}', [BorrowController::class, 'destroy'])->name('borrows.destroy');

    // Items
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{itemid}', [ItemController::class, 'show'])->name('items.show');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::put('/items/{itemid}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{itemid}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Groups
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::delete('/groups/{group_id}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student_id}', [StudentController::class, 'show'])->name('students.show'); // ✅ fixes students.show
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student_id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student_id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/register-student', [StudentRegisterController::class, 'create'])->name('student.register');
    Route::post('/register-student', [StudentRegisterController::class, 'store'])->name('student.register.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY (Users)
|--------------------------------------------------------------------------
| ✅ Only admin can access /admin/users
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});



Route::post('/submissions/{id}/add-student', [SubmissionController::class, 'addStudent'])
    ->name('submissions.addStudent');

Route::post('/submissions/{id}/approve-borrow', [SubmissionController::class, 'approveBorrow'])
    ->name('submissions.approveBorrow');

Route::get('/admin/submissions', [SubmissionController::class, 'index'])->name('submissions.index');

Route::delete('/admin/submissions/{id}/remove', [SubmissionController::class, 'remove'])
    ->name('submissions.remove');

Route::post('/admin/submissions/cancel-all', [SubmissionController::class, 'cancelAll'])
    ->name('submissions.cancelAll');
    Route::get('/register/check-phone', [App\Http\Controllers\Backend\StudentRegisterController::class, 'checkPhone'])
    ->name('register.checkPhone');
    Route::get('/register/check-student-name', [App\Http\Controllers\Backend\StudentRegisterController::class, 'checkStudentName'])
    ->name('register.checkStudentName');