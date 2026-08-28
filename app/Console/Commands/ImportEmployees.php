<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportEmployees extends Command
{
    protected $signature = 'portal:import-employees
                            {file : Path file CSV}
                            {--only-active : Import hanya karyawan aktif}';

    protected $description =
        'Import karyawan FaceLog ke Employee Portal dan buat akun otomatis';

    public function handle(): int
    {
        $file = $this->argument('file');

        /*
        |--------------------------------------------------------------------------
        | Resolve Path
        |--------------------------------------------------------------------------
        */
        if (! file_exists($file)) {
            $storagePath = storage_path('app/' . ltrim($file, '/\\'));

            if (file_exists($storagePath)) {
                $file = $storagePath;
            }
        }

        if (! file_exists($file)) {
            $this->error(
                'File tidak ditemukan: ' . $file
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Open CSV
        |--------------------------------------------------------------------------
        */
        $handle = fopen($file, 'r');

        if (! $handle) {
            $this->error('File CSV gagal dibuka.');

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */
        $headers = fgetcsv($handle);

        if (! $headers) {
            fclose($handle);

            $this->error('Header CSV tidak ditemukan.');

            return self::FAILURE;
        }

        /*
         * Bersihkan BOM dan spasi.
         */
        $headers = array_map(function ($header) {
            return trim(
                preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    (string) $header
                )
            );
        }, $headers);

        $requiredHeaders = [
            'id',
            'nama',
            'pin_fingerspot',
            'device_id',
            'jabatan',
            'tanggal_masuk',
            'status_kerja',
            'is_active',
        ];

        foreach ($requiredHeaders as $required) {
            if (! in_array($required, $headers, true)) {
                fclose($handle);

                $this->error(
                    'Kolom CSV tidak ditemukan: '
                    . $required
                );

                return self::FAILURE;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Credential Output
        |--------------------------------------------------------------------------
        |
        | Hanya akun BARU yang dimasukkan ke file ini.
        |
        */
        $credentialDir =
            storage_path('app/private/credentials');

        if (! is_dir($credentialDir)) {
            mkdir(
                $credentialDir,
                0755,
                true
            );
        }

        $credentialFile =
            $credentialDir
            . '/employee_credentials_'
            . now()->format('Ymd_His')
            . '.csv';

        $credentialHandle =
            fopen(
                $credentialFile,
                'w'
            );

        fputcsv(
            $credentialHandle,
            [
                'source_karyawan_id',
                'nama',
                'id_login',
                'password_awal',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        $total = 0;
        $created = 0;
        $updated = 0;
        $accountsCreated = 0;
        $skipped = 0;

        /*
        |--------------------------------------------------------------------------
        | Import
        |--------------------------------------------------------------------------
        */
        DB::beginTransaction();

        try {

            while (
                ($row = fgetcsv($handle))
                !== false
            ) {
                if (
                    count($row)
                    !== count($headers)
                ) {
                    $skipped++;
                    continue;
                }

                $data =
                    array_combine(
                        $headers,
                        $row
                    );

                if (! $data) {
                    $skipped++;
                    continue;
                }

                $total++;

                $sourceId =
                    (int) ($data['id'] ?? 0);

                $nama =
                    trim(
                        (string) (
                            $data['nama']
                            ?? ''
                        )
                    );

                if (
                    $sourceId <= 0 ||
                    $nama === ''
                ) {
                    $skipped++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Status Active
                |--------------------------------------------------------------------------
                */
                $isActive =
                    in_array(
                        strtolower(
                            trim(
                                (string) (
                                    $data['is_active']
                                    ?? '1'
                                )
                            )
                        ),
                        [
                            '1',
                            'true',
                            'yes',
                            'aktif',
                        ],
                        true
                    );

                if (
                    $this->option('only-active')
                    && ! $isActive
                ) {
                    $skipped++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Employee Code
                |--------------------------------------------------------------------------
                |
                | FaceLog ID 1   => A0001
                | FaceLog ID 72  => A0072
                | FaceLog ID 403 => A0403
                |
                */
                $employeeCode =
                    'A'
                    . str_pad(
                        (string) $sourceId,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

                /*
                |--------------------------------------------------------------------------
                | Employee
                |--------------------------------------------------------------------------
                */
                $employee =
                    Employee::where(
                        'source_karyawan_id',
                        $sourceId
                    )->first();

                $employeeData = [
                    'employee_code' =>
                        $employeeCode,

                    'nama' =>
                        $nama,

                    'pin_fingerspot' =>
                        $this->nullable(
                            $data['pin_fingerspot']
                            ?? null
                        ),

                    'source_device_id' =>
                        $this->nullableInt(
                            $data['device_id']
                            ?? null
                        ),

                    'jabatan' =>
                        $this->nullable(
                            $data['jabatan']
                            ?? null
                        ),

                    'tanggal_masuk' =>
                        $this->nullableDate(
                            $data['tanggal_masuk']
                            ?? null
                        ),

                    'status_kerja' =>
                        $this->nullable(
                            $data['status_kerja']
                            ?? null
                        ),

                    'is_active' =>
                        $isActive,

                    'last_synced_at' =>
                        now(),
                ];

                if ($employee) {
                    $employee->update(
                        $employeeData
                    );

                    $updated++;
                } else {
                    $employee =
                        Employee::create(
                            array_merge(
                                [
                                    'source_karyawan_id' =>
                                        $sourceId,
                                ],
                                $employeeData
                            )
                        );

                    $created++;
                }

                /*
                |--------------------------------------------------------------------------
                | User Account
                |--------------------------------------------------------------------------
                |
                | Akun existing TIDAK direset password-nya.
                |
                */
                $user = User::where(
                    'employee_id',
                    $employee->id
                )->first();

                if ($user) {
                    /*
                     * Update identitas saja.
                     */
                    $user->update([
                        'name' =>
                            $nama,

                        'username' =>
                            $employeeCode,

                        'is_active' =>
                            $isActive,
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Password awal random 6 digit
                |--------------------------------------------------------------------------
                */
                $temporaryPassword =
                    (string) random_int(
                        100000,
                        999999
                    );

                User::create([
                    'employee_id' =>
                        $employee->id,

                    'name' =>
                        $nama,

                    'username' =>
                        $employeeCode,

                    'email' =>
                        null,

                    'password' =>
                        Hash::make(
                            $temporaryPassword
                        ),

                    'role' =>
                        'karyawan',

                    'must_change_password' =>
                        true,

                    'is_active' =>
                        $isActive,
                ]);

                /*
                 * Credential plaintext hanya ditulis
                 * ke file output satu kali.
                 */
                fputcsv(
                    $credentialHandle,
                    [
                        $sourceId,
                        $nama,
                        $employeeCode,
                        $temporaryPassword,
                    ]
                );

                $accountsCreated++;
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            fclose($handle);
            fclose($credentialHandle);

            $this->error(
                'Import gagal: '
                . $e->getMessage()
            );

            return self::FAILURE;
        }

        fclose($handle);
        fclose($credentialHandle);

        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */
        $this->newLine();

        $this->info(
            'Import Employee Portal selesai.'
        );

        $this->table(
            [
                'Item',
                'Jumlah',
            ],
            [
                [
                    'Baris CSV',
                    $total,
                ],
                [
                    'Employee baru',
                    $created,
                ],
                [
                    'Employee diperbarui',
                    $updated,
                ],
                [
                    'Akun baru',
                    $accountsCreated,
                ],
                [
                    'Dilewati',
                    $skipped,
                ],
            ]
        );

        if ($accountsCreated > 0) {
            $this->newLine();

            $this->warn(
                'FILE PASSWORD AWAL:'
            );

            $this->line(
                $credentialFile
            );

            $this->newLine();

            $this->warn(
                'Simpan file tersebut dengan aman. '
                . 'Password plaintext tidak disimpan di database.'
            );
        } else {
            /*
             * Kalau tidak membuat akun baru,
             * hapus file credential kosong.
             */
            if (file_exists($credentialFile)) {
                unlink($credentialFile);
            }

            $this->info(
                'Tidak ada akun baru sehingga tidak ada file credential.'
            );
        }

        return self::SUCCESS;
    }

    protected function nullable(
        mixed $value
    ): ?string {
        $value =
            trim(
                (string) $value
            );

        if (
            $value === ''
            || strtolower($value) === 'null'
        ) {
            return null;
        }

        return $value;
    }

    protected function nullableInt(
        mixed $value
    ): ?int {
        $value =
            $this->nullable(
                $value
            );

        if (
            $value === null
            || ! is_numeric($value)
        ) {
            return null;
        }

        return (int) $value;
    }

    protected function nullableDate(
        mixed $value
    ): ?string {
        $value =
            $this->nullable(
                $value
            );

        if ($value === null) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse(
                $value
            )->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
