<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FaceLogEmployeeController extends Controller
{
    public function sync(
        Request $request
    ): JsonResponse {
        $validated = $request->validate([
            'employees' => [
                'required',
                'array',
            ],

            'employees.*.id' => [
                'required',
                'integer',
            ],

            'employees.*.nama' => [
                'required',
                'string',
                'max:255',
            ],

            'employees.*.pin_fingerspot' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employees.*.device_id' => [
                'nullable',
                'integer',
            ],

            'employees.*.jabatan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employees.*.tanggal_masuk' => [
                'nullable',
                'date',
            ],

            'employees.*.status_kerja' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employees.*.is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        $result = [
            'received' => 0,
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'new_credentials' => [],
            'errors' => [],
        ];


        foreach (
            $validated['employees']
            as $row
        ) {
            $result['received']++;

            try {

                DB::transaction(
                    function () use (
                        $row,
                        &$result
                    ) {
                        $sourceId =
                            (int) $row['id'];

                        $employee =
                            Employee::query()
                                ->where(
                                    'source_karyawan_id',
                                    $sourceId
                                )
                                ->lockForUpdate()
                                ->first();


                        $employeeCode =
                            'A' .
                            str_pad(
                                (string) $sourceId,
                                4,
                                '0',
                                STR_PAD_LEFT
                            );


                        $employeeData = [
                            'employee_code' =>
                                $employeeCode,

                            'nama' =>
                                $row['nama'],

                            'pin_fingerspot' =>
                                $row['pin_fingerspot']
                                ?? null,

                            'source_device_id' =>
                                $row['device_id']
                                ?? null,

                            'jabatan' =>
                                $row['jabatan']
                                ?? null,

                            'tanggal_masuk' =>
                                $row['tanggal_masuk']
                                ?? null,

                            'status_kerja' =>
                                $row['status_kerja']
                                ?? null,

                            'is_active' =>
                                (bool) (
                                    $row['is_active']
                                    ?? true
                                ),

                            'last_synced_at' =>
                                now(),
                        ];

                        if (! $employee) {

                            $employee =
                                Employee::create(
                                    array_merge(
                                        $employeeData,
                                        [
                                            'source_karyawan_id' =>
                                                $sourceId,
                                        ]
                                    )
                                );


                            $temporaryPassword =
                                (string) random_int(
                                    100000,
                                    999999
                                );


                            $user =
                                User::create([
                                    'name' =>
                                        $employee->nama,

                                    'employee_id' =>
                                        $employee->id,

                                    'username' =>
                                        $employeeCode,

                                    'email' =>
                                        null,

                                    'role' =>
                                        'karyawan',

                                    'password' =>
                                        Hash::make(
                                            $temporaryPassword
                                        ),

                                    'must_change_password' =>
                                        true,

                                    'is_active' =>
                                        $employee->is_active,
                                ]);


                            $result['created']++;


                            $result[
                                'new_credentials'
                            ][] = [
                                'source_karyawan_id' =>
                                    $sourceId,

                                'employee_code' =>
                                    $employeeCode,

                                'nama' =>
                                    $employee->nama,

                                'username' =>
                                    $user->username,

                                'password' =>
                                    $temporaryPassword,
                            ];


                            return;
                        }

                        $employee->update(
                            $employeeData
                        );

                        if ($employee->user) {

                            $employee->user->update([
                                'name' =>
                                    $employee->nama,

                                'username' =>
                                    $employeeCode,

                                'is_active' =>
                                    $employee->is_active,
                            ]);

                        }


                        $result['updated']++;
                    }
                );

            } catch (\Throwable $e) {

                $result['failed']++;

                $result['errors'][] = [
                    'source_karyawan_id' =>
                        $row['id']
                        ?? null,

                    'nama' =>
                        $row['nama']
                        ?? '-',

                    'message' =>
                        $e->getMessage(),
                ];

            }
        }


        return response()->json([
            'success' =>
                $result['failed'] === 0,

            'message' =>
                $result['failed'] === 0
                    ? 'Sinkronisasi karyawan berhasil.'
                    : 'Sinkronisasi selesai dengan beberapa kegagalan.',

            'data' =>
                $result,
        ]);
    }
}
