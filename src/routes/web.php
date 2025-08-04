<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceApprovalController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminAttendanceController;

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::middleware(['auth', 'verified'])->group(function () {
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
    Route::post('/login', [AdminLoginController::class, 'login']);

    // 管理者ログイン後のみアクセス可能
    Route::middleware(['auth', 'verified', 'admin'])->group(function () {
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
        Route::get('/admin/attendance/approvals', [AttendanceApprovalController::class, 'index'])->name('admin.attendance.approvals');
        Route::post('/admin/attendance/approvals/{id}/approve', [AttendanceApprovalController::class, 'approve'])->name('admin.attendance.approve');
    });
});