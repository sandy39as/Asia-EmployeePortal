<?php

use App\Http\Controllers\Api\FaceLogLeaveRequestController;
use Illuminate\Support\Facades\Route;

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
