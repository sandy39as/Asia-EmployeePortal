<?php

use App\Http\Controllers\Api\FaceLogLeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaceLogEmployeeController;

/*
|--------------------------------------------------------------------------
| FACELOG API
|--------------------------------------------------------------------------
|
| API khusus komunikasi:
|
| Employee Portal Hosting
|        ↕
| FaceLog Local
|
*/
Route::prefix('facelog')
    ->middleware('facelog.token')
    ->group(function () {

        /*
         * Sinkronisasi data.
         */
        Route::get(
            '/leave-requests',
            [
                FaceLogLeaveRequestController::class,
                'index',
            ]
        );

        /*
         * Detail satu pengajuan.
         */
        Route::get(
            '/leave-requests/{uuid}',
            [
                FaceLogLeaveRequestController::class,
                'show',
            ]
        );

        /*
         * Approve dari HRD FaceLog lokal.
         */
        Route::post(
            '/leave-requests/{uuid}/approve',
            [
                FaceLogLeaveRequestController::class,
                'approve',
            ]
        );

        /*
         * Reject dari HRD FaceLog lokal.
         */
        Route::post(
            '/leave-requests/{uuid}/reject',
            [
                FaceLogLeaveRequestController::class,
                'reject',
            ]
        );
    });

Route::prefix('facelog')
    ->middleware('facelog.token')
    ->group(function () {

        Route::post(
            '/employees/sync',
            [FaceLogEmployeeController::class, 'sync']
        );

        Route::get(
            '/leave-requests',
            [FaceLogLeaveRequestController::class, 'index']
        );

        Route::get(
            '/leave-requests/{uuid}',
            [FaceLogLeaveRequestController::class, 'show']
        );

        Route::post(
            '/leave-requests/{uuid}/approve',
            [FaceLogLeaveRequestController::class, 'approve']
        );

        Route::post(
            '/leave-requests/{uuid}/reject',
            [FaceLogLeaveRequestController::class, 'reject']
        );

    });
