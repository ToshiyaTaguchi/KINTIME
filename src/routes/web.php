<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AttendanceCorrectionController as AdminAttendanceCorrectionController;
use App\Http\Controllers\Auth\CustomRegisteredUserController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomAdminLoginController;

// ================================
// ▼ トップページ
// ================================
Route::get('/', fn() => redirect('/attendance/list'));

// ================================
// ▼ ゲスト（未認証）
// ================================
Route::middleware('guest')->group(function () {
    Route::get('/register', fn() => view('auth.register'))->name('register.form');
    Route::post('/register', [CustomRegisteredUserController::class, 'store'])->name('register');

    Route::get('/login', fn() => view('auth.login'))->name('login.form');
    Route::post('/login', [CustomLoginController::class, 'store'])->name('login');

    Route::get('/admin/login', fn() => view('admin.auth.login'))->name('admin.login.form');
    Route::post('/admin/login', [CustomAdminLoginController::class, 'store'])->name('admin.login');
});

// ================================
// ▼ 認証済み（メール認証含む）
// ================================
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::post('/email/verification-notification', function () {
        request()->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました');
    })->middleware('throttle:6,1')->name('verification.send');
});

// ================================
// ▼ 一般ユーザー用ルート（認証 + メール認証必須）
// ================================
Route::middleware(['auth', 'verified'])->group(function () {

    // 勤怠管理
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.detail');

    // 修正申請
    Route::post('/attendance/{attendance}/correction', [AttendanceCorrectionController::class, 'store'])
        ->name('attendance_correction.store');

    // 修正申請一覧（一般ユーザー用）
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'index'])
        ->name('attendance_correction.list');
});

// ================================
// ▼ 管理者用ルート（auth + can:admin）
// ================================
Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {

    // 勤怠管理
    Route::get('/attendance/list', [AdminAttendanceController::class, 'list'])->name('admin.attendance.list');
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.detail');
    Route::patch('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

    // スタッフ管理
    Route::get('/staff/list', [AdminUserController::class, 'index'])->name('admin.staff.list');
    Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'staffAttendance'])->name('admin.attendance.staff');
    Route::get('/attendance/staff/{id}/csv', [AdminAttendanceController::class, 'exportStaffAttendanceCsv'])->name('admin.attendance.staff.csv');

    // 修正申請
    Route::get('/stamp_correction_request/list', [AdminAttendanceCorrectionController::class, 'index'])
        ->name('admin.attendance_correction.list');
    Route::get('/stamp_correction_request/{attendance_correct_request_id}', [AdminAttendanceCorrectionController::class, 'show'])
        ->name('admin.attendance_correction.show');
    Route::patch('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAttendanceCorrectionController::class, 'approve'])
        ->name('admin.attendance_correction.approve');
});