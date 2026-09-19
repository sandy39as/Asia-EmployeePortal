<?php

use App\Http\Controllers\FirstLoginIdController;
use App\Http\Controllers\FirstPasswordController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Hrd\HrdEmployeeController;
use App\Http\Controllers\Hrd\HrdLeaveRequestController;

use App\Http\Controllers\Kabag\KabagLeaveRequestController;

use App\Http\Controllers\Master\EmployeeCredentialController;
use App\Http\Controllers\Master\KabagController;
use App\Http\Controllers\Master\KabagMappingController;
use App\Http\Controllers\Master\KabagSupervisorMappingController;
use App\Http\Controllers\Master\KabagIdentityMappingController;
use App\Http\Controllers\Master\PermissionTypeController;
use App\Http\Controllers\Master\SpecialLeaveTypeController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    function () {

        if (! auth()->check()) {
            return redirect()
                ->route(
                    'login'
                );
        }

        $user =
            auth()->user();


        if (
            in_array(
                $user->role,
                [
                    'hrd',
                    'admin',
                    'superadmin',
                ],
                true
            )
        ) {
            return redirect()
                ->route(
                    'hrd.leave-requests.index'
                );
        }


        if (
            $user->role === 'kabag'
        ) {
            return redirect()
                ->route(
                    'kabag.leave-requests.index'
                );
        }


        return redirect()
            ->route(
                'leave-requests.index'
            );
    }
);


/*
|--------------------------------------------------------------------------
| FIRST LOGIN ID
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/login-id/first-change',
        [
            FirstLoginIdController::class,
            'edit',
        ]
    )->name(
        'login-id.first.edit'
    );

    Route::post(
        '/login-id/first-change',
        [
            FirstLoginIdController::class,
            'update',
        ]
    )->name(
        'login-id.first.update'
    );

});


/*
|--------------------------------------------------------------------------
| PASSWORD FIRST CHANGE
|--------------------------------------------------------------------------
*/

Route::middleware(
    'auth'
)
    ->group(
        function () {

            Route::get(
                '/password/first-change',
                [
                    FirstPasswordController::class,
                    'edit',
                ]
            )
                ->name(
                    'password.first.edit'
                );


            Route::post(
                '/password/first-change',
                [
                    FirstPasswordController::class,
                    'update',
                ]
            )
                ->name(
                    'password.first.update'
                );
        }
    );


/*
|--------------------------------------------------------------------------
| KARYAWAN
|--------------------------------------------------------------------------
*/

Route::middleware(
    'auth'
)
    ->group(
        function () {

            Route::get(
                '/dashboard',
                fn () =>
                    redirect()
                        ->route(
                            'leave-requests.index'
                        )
            )
                ->name(
                    'dashboard'
                );


            Route::get(
                '/pengajuan',
                [
                    LeaveRequestController::class,
                    'index',
                ]
            )
                ->name(
                    'leave-requests.index'
                );


            Route::get(
                '/pengajuan/buat',
                [
                    LeaveRequestController::class,
                    'create',
                ]
            )
                ->name(
                    'leave-requests.create'
                );


            Route::post(
                '/pengajuan',
                [
                    LeaveRequestController::class,
                    'store',
                ]
            )
                ->name(
                    'leave-requests.store'
                );


            Route::get(
                '/pengajuan/{leaveRequest}',
                [
                    LeaveRequestController::class,
                    'show',
                ]
            )
                ->name(
                    'leave-requests.show'
                );


            Route::post(
                '/pengajuan/{leaveRequest}/cancel',
                [
                    LeaveRequestController::class,
                    'cancel',
                ]
            )
                ->name(
                    'leave-requests.cancel'
                );
        }
    );


/*
|--------------------------------------------------------------------------
| HRD
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'hrd',
])
    ->prefix(
        'hrd'
    )
    ->name(
        'hrd.'
    )
    ->group(
        function () {

            Route::get(
                '/dashboard',
                fn () =>
                    redirect()
                        ->route(
                            'hrd.leave-requests.index'
                        )
            )
                ->name(
                    'dashboard'
                );


            Route::get(
                '/pengajuan',
                [
                    HrdLeaveRequestController::class,
                    'index',
                ]
            )
                ->name(
                    'leave-requests.index'
                );


            Route::get(
                '/pengajuan/{leaveRequest}',
                [
                    HrdLeaveRequestController::class,
                    'show',
                ]
            )
                ->name(
                    'leave-requests.show'
                );


            Route::post(
                '/pengajuan/{leaveRequest}/approve',
                [
                    HrdLeaveRequestController::class,
                    'approve',
                ]
            )
                ->name(
                    'leave-requests.approve'
                );


            Route::post(
                '/pengajuan/{leaveRequest}/reject',
                [
                    HrdLeaveRequestController::class,
                    'reject',
                ]
            )
                ->name(
                    'leave-requests.reject'
                );


            Route::get(
                '/karyawan',
                [
                    HrdEmployeeController::class,
                    'index',
                ]
            )
                ->name(
                    'employees.index'
                );


            Route::post(
                '/karyawan/{employee}/reset-password',
                [
                    HrdEmployeeController::class,
                    'resetPassword',
                ]
            )
                ->name(
                    'employees.reset-password'
                );
        }
    );


/*
|--------------------------------------------------------------------------
| KABAG
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'kabag',
])
    ->prefix(
        'kabag'
    )
    ->name(
        'kabag.'
    )
    ->group(
        function () {

            Route::get(
                '/pengajuan',
                [
                    KabagLeaveRequestController::class,
                    'index',
                ]
            )
                ->name(
                    'leave-requests.index'
                );


            Route::post(
                '/pengajuan/{leaveRequest}/approve',
                [
                    KabagLeaveRequestController::class,
                    'approve',
                ]
            )
                ->name(
                    'leave-requests.approve'
                );


            Route::post(
                '/pengajuan/{leaveRequest}/reject',
                [
                    KabagLeaveRequestController::class,
                    'reject',
                ]
            )
                ->name(
                    'leave-requests.reject'
                );
        }
    );


/*
|--------------------------------------------------------------------------
| MASTER DATA
|--------------------------------------------------------------------------
|
| Semua route master hanya untuk master admin.
|
| Prefix sudah "master", jadi route di dalam group JANGAN ditulis
| "/master/..." lagi.
|
| Name prefix sudah "master.", jadi name di dalam group JANGAN ditulis
| "master...." lagi.
|
*/

Route::middleware([
    'auth',
    'portal.master-admin',
])
    ->prefix(
        'master'
    )
    ->name(
        'master.'
    )
    ->group(
        function () {

            /*
            |--------------------------------------------------------------------------
            | MASTER KABAG
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'kabag',
                KabagController::class
            )
                ->except(
                    'show'
                );

            /*
            |--------------------------------------------------------------------------
            | EMPLOYEE TEMP CREDENTIALS
            |-------------------------------------------------------------------------
            */

            Route::get(
                '/employee-credentials',
                [
                    \App\Http\Controllers\Master\EmployeeCredentialController::class,
                    'index',
                ]
            )->name('employee-credentials.index');

            Route::post(
                '/employee-credentials/mass-reset',
                [
                    \App\Http\Controllers\Master\EmployeeCredentialController::class,
                    'massReset',
                ]
            )->name('employee-credentials.mass-reset');

            Route::post(
                '/employee-credentials/{employee}/reset',
                [
                    \App\Http\Controllers\Master\EmployeeCredentialController::class,
                    'resetOne',
                ]
            )->name('employee-credentials.reset-one');

            Route::get(
                '/employee-credentials/export',
                [
                    \App\Http\Controllers\Master\EmployeeCredentialController::class,
                    'export',
                ]
            )->name('employee-credentials.export');

            Route::post(
                '/employee-credentials/mass-reset-batch',
                [
                    EmployeeCredentialController::class,
                    'batchReset',
                ]
            )->name(
                'employee-credentials.mass-reset-batch'
            );


            /*
            |--------------------------------------------------------------------------
            | MAPPING KABAG
            |--------------------------------------------------------------------------
            |
            | Route name:
            |
            | master.kabag-mapping.index
            | master.kabag-mapping.update
            |
            */

            Route::get(
                '/kabag-mapping',
                [KabagMappingController::class, 'index']
            )->name(
                'kabag-mapping.index'
            );

            Route::post(
                '/kabag-mapping/{kabag}/assign',
                [KabagMappingController::class, 'assign']
            )->name(
                'kabag-mapping.assign'
            );

            Route::post(
                '/kabag-mapping/{kabag}/assign-filtered',
                [KabagMappingController::class, 'assignFiltered']
            )->name(
                'kabag-mapping.assign-filtered'
            );

            Route::delete(
                '/kabag-mapping/{kabag}/employee/{employee}',
                [KabagMappingController::class, 'remove']
            )->name(
                'kabag-mapping.remove'
            );


            /*
            |--------------------------------------------------------------------------
            | MAPPING IDENTITAS KABAG
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/kabag-identity-mapping',
                [
                    KabagIdentityMappingController::class,
                    'index',
                ]
            )->name(
                'kabag-identity-mapping.index'
            );

            Route::post(
                '/kabag-identity-mapping/{kabag}',
                [
                    KabagIdentityMappingController::class,
                    'update',
                ]
            )->name(
                'kabag-identity-mapping.update'
            );


            /*
            |--------------------------------------------------------------------------
            | MAPPING ATASAN KABAG
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/kabag-supervisor-mapping',
                [
                    KabagSupervisorMappingController::class,
                    'index',
                ]
            )->name(
                'kabag-supervisor-mapping.index'
            );


            Route::post(
                '/kabag-supervisor-mapping/{kabag}',
                [
                    KabagSupervisorMappingController::class,
                    'update',
                ]
            )->name(
                'kabag-supervisor-mapping.update'
            );


            /*
            |--------------------------------------------------------------------------
            | MASTER CUTI KHUSUS
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'special-leave-types',
                SpecialLeaveTypeController::class
            )
                ->except([
                    'create',
                    'edit',
                    'show',
                ]);


            /*
            |--------------------------------------------------------------------------
            | MASTER JENIS IZIN
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'permission-types',
                PermissionTypeController::class
            )
                ->except([
                    'create',
                    'edit',
                    'show',
                ]);
        }
    );


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->group(function () {

        Route::get(
            '/profile',
            [
                ProfileController::class,
                'edit',
            ]
        )->name(
            'profile.edit'
        );


        Route::post(
            '/profile/check-username',
            [
                ProfileController::class,
                'checkUsername',
            ]
        )->name(
            'profile.check-username'
        );


        Route::patch(
            '/profile',
            [
                ProfileController::class,
                'update',
            ]
        )->name(
            'profile.update'
        );


        Route::delete(
            '/profile',
            [
                ProfileController::class,
                'destroy',
            ]
        )->name(
            'profile.destroy'
        );

    });


require __DIR__ . '/auth.php';
