<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffAttendanceController;
use App\Http\Controllers\Admin\StampCorrectionRequestController as AdminStampCorrectionRequestController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/break/start', [AttendanceController::class, 'breakStart'])->name('attendance.break.start');
    Route::post('/attendance/break/end', [AttendanceController::class, 'breakEnd'])->name('attendance.break.end');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');

    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');

    Route::post('/stamp_correction_request', [StampCorrectionRequestController::class, 'store'])->name('stamp_correction_request.store');
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('stamp_correction_request.list');
});

Route::prefix('admin')->name('admin.')->group(function () {

    // 管理者ログイン
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->name('login.store');

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/staff/list', [StaffController::class, 'index'])->name('staff.list');

        Route::get('/attendance/staff/{user}', [StaffAttendanceController::class, 'index'])->name('attendance.staff');
        
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('attendance.list');
        Route::get('/attendance/{user}/{date}', [AdminAttendanceController::class, 'show'])->name('attendance.show');
        Route::post('/attendance/{user}/{date}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        Route::get('/stamp_correction_request/list', [AdminStampCorrectionRequestController::class, 'index'])->name('stamp_correction_request.list');
        Route::get('/stamp_correction_request/approve/{correctionRequest}', [AdminStampCorrectionRequestController::class, 'show'])->name('stamp_correction_request.approve');
        Route::post('/stamp_correction_request/approve/{correctionRequest}', [AdminStampCorrectionRequestController::class, 'approve'])->name('stamp_correction_request.approve.update');
    });
});