<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FirstPasswordController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hrd\HrdDashboardController;
use App\Http\Controllers\Hrd\HrdLeaveRequestController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
|
| Setelah logout Laravel Breeze biasanya redirect ke "/".
| Jadi route ini wajib ada.
|
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| FIRST PASSWORD CHANGE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get(
        '/password/first-change',
        [FirstPasswordController::class, 'edit']
    )->name('password.first.edit');

    Route::post(
        '/password/first-change',
        [FirstPasswordController::class, 'update']
    )->name('password.first.update');
});

/*
|--------------------------------------------------------------------------
| EMPLOYEE PORTAL
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/pengajuan',
        [LeaveRequestController::class, 'index']
    )->name('leave-requests.index');

    Route::get(
        '/pengajuan/buat',
        [LeaveRequestController::class, 'create']
    )->name('leave-requests.create');

    Route::post(
        '/pengajuan',
        [LeaveRequestController::class, 'store']
    )->name('leave-requests.store');

    Route::get(
        '/pengajuan/{leaveRequest}',
        [LeaveRequestController::class, 'show']
    )->name('leave-requests.show');

    Route::post(
        '/pengajuan/{leaveRequest}/cancel',
        [LeaveRequestController::class, 'cancel']
    )->name('leave-requests.cancel');
});

/*
|--------------------------------------------------------------------------
| HRD
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'hrd',
])->prefix('hrd')
    ->name('hrd.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [HrdDashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/pengajuan',
            [HrdLeaveRequestController::class, 'index']
        )->name('leave-requests.index');

        Route::get(
            '/pengajuan/{leaveRequest}',
            [HrdLeaveRequestController::class, 'show']
        )->name('leave-requests.show');

        Route::post(
            '/pengajuan/{leaveRequest}/approve',
            [HrdLeaveRequestController::class, 'approve']
        )->name('leave-requests.approve');

        Route::post(
            '/pengajuan/{leaveRequest}/reject',
            [HrdLeaveRequestController::class, 'reject']
        )->name('leave-requests.reject');
    });

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
