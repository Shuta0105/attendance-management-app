<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\RequestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/stamp_correction_request/list', function () {})->middleware(['auth', 'switch.request.list']);

// 一般ユーザー
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/attendance/list', [AttendanceController::class, 'list']);
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail']);

    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/attendance/break-start', [BreakTimeController::class, 'breakStart']);
    Route::post('/attendance/break-end', [BreakTimeController::class, 'breakEnd']);
    Route::post('/attendance/modify/{id}', [RequestController::class, 'request']);
});

// 管理者
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminController::class, 'index']);
    Route::get('/admin/attendance/{id}', [AdminController::class, 'detail']);
    Route::get('/admin/staff/list', [AdminController::class, 'staffList']);
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffDetail']);
    Route::get(
        '/stamp_correction_request/approve/{request_id}',
        [AdminRequestController::class, 'detail']
    );
    Route::get('/admin/attendance/staff/{id}/csv', [AdminController::class, 'exportCsv']);

    Route::post('/admin/modify/{id}', [AdminController::class, 'modify']);
    Route::post(
        '/stamp_correction_request/approve/{request_id}',
        [AdminRequestController::class, 'approve']
    );
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

// メール認証
Route::get('/email/verify', function () {
    return view('auth.email-verify');
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6.1'])->name('verification.send');
