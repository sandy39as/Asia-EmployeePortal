<?php

use App\Http\Controllers\FirstPasswordController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Hrd\HrdLeaveRequestController;
use App\Http\Controllers\Hrd\HrdEmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\KabagController;
use App\Http\Controllers\Master\KabagMappingController;

// Redirect Home
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if (in_array($user->role, ['hrd', 'admin', 'superadmin'], true)) {
        return redirect()->route('hrd.leave-requests.index');
    }

    return redirect()->route('leave-requests.index');
});

// Password First Change
Route::middleware('auth')->group(function () {
    Route::get('/password/first-change', [FirstPasswordController::class, 'edit'])->name('password.first.edit');
    Route::post('/password/first-change', [FirstPasswordController::class, 'update'])->name('password.first.update');
});

// Karyawan Routes
Route::middleware('auth')->group(function () {
    // Redirect alias dashboard lama ke pengajuan
    Route::get('/dashboard', fn() => redirect()->route('leave-requests.index'))->name('dashboard');

    Route::get('/pengajuan', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/pengajuan/buat', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/pengajuan', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::post('/pengajuan/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
});

// HRD Routes
Route::middleware(['auth', 'hrd'])
    ->prefix('hrd')
    ->name('hrd.')
    ->group(function () {
        // Redirect alias dashboard HRD lama ke pengajuan
        Route::get('/dashboard', fn() => redirect()->route('hrd.leave-requests.index'))->name('dashboard');

        Route::get('/pengajuan', [HrdLeaveRequestController::class, 'index'])->name('leave-requests.index');
        Route::get('/pengajuan/{leaveRequest}', [HrdLeaveRequestController::class, 'show'])->name('leave-requests.show');
        Route::post('/pengajuan/{leaveRequest}/approve', [HrdLeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('/pengajuan/{leaveRequest}/reject', [HrdLeaveRequestController::class, 'reject'])->name('leave-requests.reject');

        Route::get('/karyawan', [HrdEmployeeController::class, 'index'])->name('employees.index');
        Route::post('/karyawan/{employee}/reset-password', [HrdEmployeeController::class, 'resetPassword'])->name('employees.reset-password');
    });

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Kabag Routes
Route::middleware([
    'auth',
    'portal.master-admin',
])
    ->prefix('master')
    ->name('master.')
    ->group(function () {

        Route::resource(
            'kabag',
            KabagController::class
        )->except('show');

        Route::get(
            '/kabag-mapping',
            [KabagMappingController::class, 'index']
        )->name('kabag-mapping.index');

        Route::get(
            '/kabag-mapping/{kabag}',
            [KabagMappingController::class, 'show']
        )->name('kabag-mapping.show');

        Route::post(
            '/kabag-mapping/{kabag}/assign',
            [KabagMappingController::class, 'assign']
        )->name('kabag-mapping.assign');

        Route::delete(
            '/kabag-mapping/{kabag}/employee/{employee}',
            [KabagMappingController::class, 'remove']
        )->name('kabag-mapping.remove');

    });

require __DIR__.'/auth.php';
