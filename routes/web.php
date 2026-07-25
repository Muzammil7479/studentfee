<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| Guest routes (login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
| Every route below requires a logged-in user (admin or user). Destructive
| or admin-only actions are additionally protected with the 'admin'
| middleware.
*/
Route::middleware('auth')->group(function () {

    // Root now points at the role-aware dashboard.
    Route::get('/', [DashboardController::class, 'index'])->name('root');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Original module hub, kept intact for backwards compatibility.
    Route::get('/portal', function () {
        return view('portal');
    })->name('portal');

    // Profile & password
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');

    /*
    |----------------------------------------------------------------------
    | Accounts / Fees / Payments / Receipts
    |----------------------------------------------------------------------
    | Both roles can view and add payments. Editing/deleting fee
    | structures, payments, and fines is restricted to admins only,
    | per "no ability to delete important records" for the user role.
    */
    Route::get('/account-section', [AccountController::class, 'index'])->name('account.dashboard');
    Route::post('/account-section/add-payment', [AccountController::class, 'addPayment'])->name('account.addPayment');
    Route::get('/account-section/payment/{paymentId}/receipt', [AccountController::class, 'printReceipt'])->name('account.receipt.print');
    Route::get('/account-section/payment/{paymentId}/receipt/download', [AccountController::class, 'downloadReceipt'])->name('account.receipt.download');

    Route::middleware('admin')->group(function () {
        Route::post('/account-section/create-class-plan', [AccountController::class, 'createClassPlan'])->name('account.createClassPlan');
        Route::put('/account-section/fee-structure/{feeStructureId}', [AccountController::class, 'updateClassPlan'])->name('account.feeStructure.update');
        Route::delete('/account-section/fee-structure/{feeStructureId}', [AccountController::class, 'deleteClassPlan'])->name('account.feeStructure.delete');
        Route::post('/account-section/apply-scholarship', [AccountController::class, 'applyScholarship'])->name('account.applyScholarship');
        Route::post('/account-section/assign-student-fee', [AccountController::class, 'assignStudentFee'])->name('account.assignStudentFee');
        Route::put('/account-section/payment/{paymentId}', [AccountController::class, 'updatePayment'])->name('account.payment.update');
        Route::delete('/account-section/payment/{paymentId}', [AccountController::class, 'deletePayment'])->name('account.payment.delete');
        Route::post('/account-section/add-fine', [AccountController::class, 'addFine'])->name('account.addFine');
    });

    /*
    |----------------------------------------------------------------------
    | Student Management (Admin)
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminStudentController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students');
        Route::post('/admin/students/store', [AdminStudentController::class, 'store'])->name('admin.students.store');
        Route::get('/admin/students/{id}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
        Route::post('/admin/students/{id}/update', [AdminStudentController::class, 'update'])->name('admin.students.update');
        Route::delete('/admin/students/{id}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');

        // User Management
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

        // Settings
        Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings');
    });

    /*
    |----------------------------------------------------------------------
    | Teacher Management (Admin only)
    |----------------------------------------------------------------------
    | Not listed in the standard "user" feature set, so the whole module
    | -- including read-only views -- is restricted to admins.
    */
    Route::middleware('admin')->group(function () {
        Route::get('/teacher', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');

        // NOTE: /teachers/create must stay registered before /teachers/{id}
        // so the literal segment isn't swallowed by the wildcard route.
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');

        Route::get('/teachers/{id}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Student view / Reports (both roles)
    |----------------------------------------------------------------------
    */
    Route::get('/student', [StudentController::class, 'index'])->name('student.dashboard');
    Route::get('/student/payment/{paymentId}/receipt', [AccountController::class, 'printReceipt'])->name('student.receipt.print');
    Route::get('/student/payment/{paymentId}/receipt/download', [AccountController::class, 'downloadReceipt'])->name('student.receipt.download');

    Route::get('/principal', [PrincipalController::class, 'index'])->name('principal.dashboard');
});
