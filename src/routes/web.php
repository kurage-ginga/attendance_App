<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceApprovalController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminAttendanceController;

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::middleware(['auth:employee', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
    Route::post('/break/start', [AttendanceController::class, 'startBreak'])->name('break.start');
    Route::post('/break/end', [AttendanceController::class, 'endBreak'])->name('break.end');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{date}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/{date}/update', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/requests', [AttendanceController::class, 'requestList'])->name('attendance.requests');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // 管理者ログイン画面表示
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    // 管理者ログイン処理
    Route::post('/login', [AdminLoginController::class, 'adminLogin']);

    // 管理者ログイン後のみアクセス可能
    Route::middleware(['auth:admin', 'verified', 'admin'])->group(function () {
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
        Route::get('/attendance/detail/{id}', [AdminAttendanceController::class, 'detail'])->name('attendance.detail');
        Route::put('/attendance/{id}/update', [AdminAttendanceController::class, 'update'])->name('attendance.update');
        Route::get('/attendance/approvals', [AttendanceApprovalController::class, 'index'])->name('attendance.approvals');
        Route::post('/attendance/approvals/{id}/approve', [AttendanceApprovalController::class, 'approve'])->name('attendance.approve');
        Route::get('/staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/{id}/attendance/{year}/{month?}', [AdminAttendanceController::class, 'monthly'])
            ->name('attendance.monthly');
        Route::get('/attendance/approvals/{id}', [AttendanceApprovalController::class, 'show'])
            ->name('attendance.approvals.show');
        Route::get('/staff/{id}/attendance/{year}/{month}/csv', [AdminAttendanceController::class, 'csvExport'])
            ->name('attendance.csv');
    });
});