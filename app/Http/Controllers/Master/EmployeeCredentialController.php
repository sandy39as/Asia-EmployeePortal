<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeTempCredential;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class EmployeeCredentialController extends Controller
{
    private const MASTER_EMAIL = 'sandyramdani65@gmail.com';

    public function index(Request $request): View
    {
        $this->authorizeMaster($request);

        $filters = $this->validatedFilters($request);

        $categories = Employee::query()
            ->where('is_active', true)
            ->whereNotNull('source_kategori_karyawan_name')
            ->where('source_kategori_karyawan_name', '<>', '')
            ->distinct()
            ->orderBy('source_kategori_karyawan_name')
            ->pluck('source_kategori_karyawan_name');

        $employeesQuery = Employee::query()
            ->with('user')
            ->where('is_active', true)
            ->whereHas('user');

        $this->applyEmployeeFilters($employeesQuery, $filters);

        $employees = $employeesQuery
            ->orderBy('source_kategori_karyawan_name')
            ->orderBy('nama')
            ->get();

        $credentialQuery = EmployeeTempCredential::query()
            ->with(['employee', 'user'])
            ->whereHas('user', fn ($query) =>
                $query->where('must_change_password', true)
            )
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        $this->applyCredentialFilters($credentialQuery, $filters);

        $credentials = $credentialQuery
            ->latest('generated_at')
            ->get();

        $summary = [
            'matching_employees' => (clone $employeesQuery)->count(),

            'valid_credentials' => (clone $credentialQuery)->count(),

            'must_change_password' => Employee::query()
                ->where('is_active', true)
                ->whereHas('user', fn ($query) =>
                    $query->where('must_change_password', true)
                )
                ->count(),
        ];

        return view(
            'master.employee-credentials.index',
            compact(
                'employees',
                'credentials',
                'categories',
                'filters',
                'summary'
            )
        );
    }

    public function massReset(Request $request): RedirectResponse
    {
        $this->authorizeMaster($request);

        @set_time_limit(0);

        $validated = $request->validate([
            'area' => [
                'nullable',
                Rule::in(['52', '27', 'other']),
            ],
            'category' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'allow_all' => ['nullable', 'boolean'],

            'reset_mode' => [
                'required',
                Rule::in([
                    'password_only',
                    'login_and_password',
                ]),
            ],

            'employee_ids' => [
                'nullable',
                'array',
                'min:1',
            ],

            'employee_ids.*' => [
                'integer',
                Rule::exists('employees', 'id')->where(
                    fn ($query) => $query->where(
                        'is_active',
                        true
                    )
                ),
            ],
        ]);

        $area = trim(
            (string) ($validated['area'] ?? '')
        );

        $category = trim(
            (string) ($validated['category'] ?? '')
        );

        $search = trim(
            (string) ($validated['search'] ?? '')
        );

        $allowAll =
            (bool) ($validated['allow_all'] ?? false);

        $resetMode =
            $validated['reset_mode'];

        $selectedEmployeeIds = collect(
            $validated['employee_ids'] ?? []
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        $resetSelectedOnly =
            $selectedEmployeeIds->isNotEmpty();

        if (
            ! $resetSelectedOnly
            && $area === ''
            && $category === ''
            && $search === ''
            && ! $allowAll
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'mass_reset' =>
                        'Pilih Area/Bagian/Search, atau centang konfirmasi reset seluruh akun aktif.',
                ]);
        }

        $filters = [
            'area' => $area,
            'category' => $category,
            'search' => $search,
        ];

        $employeesQuery = Employee::query()
            ->with('user')
            ->where('is_active', true)
            ->whereHas('user');

        if ($resetSelectedOnly) {
            $employeesQuery->whereIn(
                'id',
                $selectedEmployeeIds->all()
            );
        } else {
            $this->applyEmployeeFilters(
                $employeesQuery,
                $filters
            );
        }

        $targetCount =
            (clone $employeesQuery)->count();

        if ($targetCount < 1) {
            return back()->withErrors([
                'mass_reset' =>
                    'Tidak ada akun karyawan yang cocok dengan filter.',
            ]);
        }

        $successCount = 0;
        $failedCount = 0;
        $failedEmployees = [];

        $createdBy =
            $request->user()->id;

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION PER USER
        |--------------------------------------------------------------------------
        |
        | Jangan bungkus seluruh mass reset ke satu transaction besar.
        | Satu row yang terkunci tidak boleh menggagalkan seluruh batch.
        |
        */

        $employeesQuery
            ->orderBy('id')
            ->chunkById(
                50,
                function ($employees) use (
                    $resetMode,
                    $createdBy,
                    &$successCount,
                    &$failedCount,
                    &$failedEmployees
                ) {
                    foreach ($employees as $employee) {
                        $user = $employee->user;

                        if (! $user) {
                            continue;
                        }

                        try {
                            $this->resetCredentialForEmployee(
                                $employee,
                                $user,
                                $resetMode,
                                $createdBy
                            );

                            $successCount++;
                        } catch (Throwable $e) {
                            report($e);

                            $failedCount++;

                            if (count($failedEmployees) < 20) {
                                $failedEmployees[] =
                                    $employee->nama
                                    . ' ('
                                    . $employee->employee_code
                                    . ')';
                            }
                        }
                    }
                },
                'id'
            );

        $modeLabel =
            $resetSelectedOnly
                ? 'karyawan terpilih'
                : 'hasil filter';

        $resetLabel =
            $resetMode === 'login_and_password'
                ? 'ID Login + password'
                : 'password';

        $message =
            $successCount
            . ' dari '
            . $targetCount
            . ' akun '
            . $modeLabel
            . ' berhasil direset '
            . $resetLabel
            . '.';

        if ($failedCount > 0) {
            $message .=
                ' '
                . $failedCount
                . ' akun gagal/terkunci dan dilewati.';

            if (! empty($failedEmployees)) {
                $message .=
                    ' Gagal: '
                    . implode(', ', $failedEmployees);

                if ($failedCount > count($failedEmployees)) {
                    $message .= ', dan lainnya';
                }

                $message .= '.';
            }
        } else {
            $message .=
                ' Password wajib diganti saat login.';
        }

        return redirect()
            ->route(
                'master.employee-credentials.index',
                array_filter(
                    $filters,
                    fn ($value) => $value !== ''
                )
            )
            ->with(
                $failedCount > 0
                    ? 'warning'
                    : 'success',
                $message
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX BATCH RESET
    |--------------------------------------------------------------------------
    |
    | Maksimal 10 akun per HTTP request agar tidak terkena Nginx 504.
    | Frontend akan membagi ratusan akun menjadi beberapa request kecil
    | dan menampilkan progress secara langsung.
    |
    */

    public function batchReset(
        Request $request
    ): \Illuminate\Http\JsonResponse {
        $this->authorizeMaster(
            $request
        );

        @set_time_limit(60);

        $validated =
            $request->validate([
                'reset_mode' => [
                    'required',
                    Rule::in([
                        'password_only',
                        'login_and_password',
                    ]),
                ],

                'employee_ids' => [
                    'required',
                    'array',
                    'min:1',
                    'max:10',
                ],

                'employee_ids.*' => [
                    'integer',
                    Rule::exists(
                        'employees',
                        'id'
                    )->where(
                        fn ($query) =>
                            $query->where(
                                'is_active',
                                true
                            )
                    ),
                ],
            ]);

        $resetMode =
            $validated[
                'reset_mode'
            ];

        $employeeIds =
            collect(
                $validated[
                    'employee_ids'
                ]
            )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values();

        $employees =
            Employee::query()
                ->with(
                    'user'
                )
                ->where(
                    'is_active',
                    true
                )
                ->whereIn(
                    'id',
                    $employeeIds
                )
                ->get()
                ->keyBy(
                    'id'
                );

        $successCount =
            0;

        $failedCount =
            0;

        $failed =
            [];

        $createdBy =
            $request
                ->user()
                ->id;

        foreach (
            $employeeIds
            as $employeeId
        ) {
            $employee =
                $employees->get(
                    $employeeId
                );

            if (
                ! $employee
                ||
                ! $employee->user
            ) {
                $failedCount++;

                $failed[] = [
                    'employee_id' =>
                        $employeeId,

                    'nama' =>
                        $employee?->nama
                        ?? 'Tidak ditemukan',

                    'employee_code' =>
                        $employee?->employee_code
                        ?? '-',

                    'message' =>
                        'Akun login tidak tersedia.',
                ];

                continue;
            }

            try {
                $this->resetCredentialForEmployee(
                    $employee,
                    $employee->user,
                    $resetMode,
                    $createdBy
                );

                $successCount++;
            } catch (
                Throwable $e
            ) {
                report(
                    $e
                );

                $failedCount++;

                $failed[] = [
                    'employee_id' =>
                        $employee->id,

                    'nama' =>
                        $employee->nama,

                    'employee_code' =>
                        $employee->employee_code,

                    'message' =>
                        $this->safeBatchErrorMessage(
                            $e
                        ),
                ];
            }
        }

        return response()->json([
            'success' =>
                true,

            'processed' =>
                $successCount
                +
                $failedCount,

            'success_count' =>
                $successCount,

            'failed_count' =>
                $failedCount,

            'failed' =>
                $failed,
        ]);
    }


    public function resetOne(
        Request $request,
        Employee $employee
    ) {
        $this->authorizeMaster($request);

        $validated =
            $request->validate([
                'reset_mode' => [
                    'nullable',
                    Rule::in([
                        'password_only',
                        'login_and_password',
                    ]),
                ],
            ]);

        $resetMode =
            $validated['reset_mode']
            ?? 'password_only';

        $employee->load('user');

        abort_unless(
            $employee->user,
            422,
            'Akun login karyawan tidak tersedia.'
        );

        $plainPassword = (string) random_int(
            100000,
            999999
        );

        $this->resetCredentialForEmployee(
            $employee,
            $employee->user,
            $resetMode,
            $request->user()->id,
            $plainPassword
        );

        $employee->user->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset.',
                'data' => [
                    'username' =>
                        $employee
                            ->user
                            ->username,

                    'password' =>
                        $plainPassword,

                    'reset_mode' =>
                        $resetMode,
                ],
            ]);
        }

        return back()->with(
            'success',
            'Password '
            . $employee->nama
            . ' berhasil direset.'
        );
    }

    public function export(
        Request $request
    ): StreamedResponse {
        $this->authorizeMaster($request);

        $filters = $this->validatedFilters($request);

        $query = EmployeeTempCredential::query()
            ->with(['employee', 'user'])
            ->whereHas('user', fn ($userQuery) =>
                $userQuery->where(
                    'must_change_password',
                    true
                )
            )
            ->where(function ($credentialQuery) {
                $credentialQuery
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        $this->applyCredentialFilters(
            $query,
            $filters
        );

        $credentials = $query
            ->orderBy('generated_at', 'desc')
            ->get();

        abort_if(
            $credentials->isEmpty(),
            422,
            'Tidak ada password sementara yang dapat diekspor.'
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kredensial Login');

        $headers = [
            'No',
            'ID Login',
            'Nama',
            'Jabatan',
            'Kategori',
            'Area',
            'Password Sementara',
            'Generated At',
            'Berlaku Sampai',
        ];

        $sheet->fromArray(
            $headers,
            null,
            'A1'
        );

        $sheet
            ->getStyle('A1:I1')
            ->getFont()
            ->setBold(true);

        $row = 2;

        foreach (
            $credentials as $index => $credential
        ) {
            $employee = $credential->employee;
            $user = $credential->user;

            if (! $employee || ! $user) {
                continue;
            }

            $area = match (
                (int) $employee->source_device_id
            ) {
                1, 2 => '52',
                3 => '27',
                default => 'Lain',
            };

            $sheet->fromArray(
                [
                    $index + 1,
                    $user->username
                        ?: $employee->employee_code,
                    $employee->nama,
                    $employee->jabatan ?: '-',
                    $employee->source_kategori_karyawan_name ?: '-',
                    $area,
                    $credential->password_encrypted,
                    $credential->generated_at?->format('d/m/Y H:i'),
                    $credential->expires_at?->format('d/m/Y H:i'),
                ],
                null,
                'A' . $row
            );

            $row++;
        }

        foreach (range('A', 'I') as $column) {
            $sheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $sheet
            ->getStyle('A:I')
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        EmployeeTempCredential::query()
            ->whereIn(
                'id',
                $credentials->pluck('id')
            )
            ->update([
                'exported_at' => now(),
            ]);

        $filename =
            'kredensial-karyawan-'
            . now()->format('Ymd-His')
            . '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    private function resetCredentialForEmployee(
        Employee $employee,
        $user,
        string $resetMode,
        int $createdBy,
        ?string $plainPassword = null
    ): string {
        $plainPassword =
            $plainPassword
            ?? (string) random_int(
                100000,
                999999
            );

        $maxAttempts = 3;
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                DB::transaction(
                    function () use (
                        $employee,
                        $user,
                        $resetMode,
                        $createdBy,
                        $plainPassword
                    ) {
                        $lockedUser =
                            \App\Models\User::query()
                                ->whereKey($user->id)
                                ->lockForUpdate()
                                ->firstOrFail();

                        $userData = [
                            'password' =>
                                Hash::make(
                                    $plainPassword
                                ),

                            'must_change_password' =>
                                true,
                        ];

                        if (
                            $resetMode ===
                            'login_and_password'
                        ) {
                            $userData['username'] =
                                $employee->employee_code;

                            $userData['must_change_username'] =
                                true;
                        }

                        $lockedUser
                            ->forceFill($userData)
                            ->save();

                        EmployeeTempCredential::updateOrCreate(
                            [
                                'user_id' =>
                                    $lockedUser->id,
                            ],
                            [
                                'employee_id' =>
                                    $employee->id,

                                'password_encrypted' =>
                                    $plainPassword,

                                'created_by' =>
                                    $createdBy,

                                'generated_at' =>
                                    now(),

                                'exported_at' =>
                                    null,

                                'expires_at' =>
                                    now()->addDays(7),
                            ]
                        );
                    }
                );

                return $plainPassword;

            } catch (QueryException $e) {
                $mysqlError =
                    (int) ($e->errorInfo[1] ?? 0);

                $isLockError =
                    in_array(
                        $mysqlError,
                        [1205, 1213],
                        true
                    );

                if (
                    ! $isLockError
                    || $attempt >= $maxAttempts
                ) {
                    throw $e;
                }

                usleep(
                    250000 * $attempt
                );
            }
        }
    }


    private function validatedFilters(
        Request $request
    ): array {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'category' => [
                'nullable',
                'string',
                'max:255',
            ],
            'area' => [
                'nullable',
                Rule::in(['52', '27', 'other']),
            ],
        ]);

        return [
            'search' => trim(
                (string) ($validated['search'] ?? '')
            ),
            'category' => trim(
                (string) ($validated['category'] ?? '')
            ),
            'area' => trim(
                (string) ($validated['area'] ?? '')
            ),
        ];
    }

    private function applyEmployeeFilters(
        Builder $query,
        array $filters
    ): void {
        $search = $filters['search'] ?? '';
        $category = $filters['category'] ?? '';
        $area = $filters['area'] ?? '';

        $query
            ->when(
                $search !== '',
                function (
                    Builder $builder
                ) use ($search) {
                    $builder->where(
                        function (
                            Builder $subQuery
                        ) use ($search) {
                            $subQuery
                                ->where(
                                    'nama',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'employee_code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'jabatan',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'source_kategori_karyawan_name',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $category !== '',
                fn (Builder $builder) =>
                    $builder->where(
                        'source_kategori_karyawan_name',
                        $category
                    )
            )
            ->when(
                $area === '52',
                fn (Builder $builder) =>
                    $builder->whereIn(
                        'source_device_id',
                        [1, 2]
                    )
            )
            ->when(
                $area === '27',
                fn (Builder $builder) =>
                    $builder->where(
                        'source_device_id',
                        3
                    )
            )
            ->when(
                $area === 'other',
                fn (Builder $builder) =>
                    $builder->where(
                        function (
                            Builder $subQuery
                        ) {
                            $subQuery
                                ->whereNull(
                                    'source_device_id'
                                )
                                ->orWhereNotIn(
                                    'source_device_id',
                                    [1, 2, 3]
                                );
                        }
                    )
            );
    }

    private function applyCredentialFilters(
        Builder $query,
        array $filters
    ): void {
        $query->whereHas(
            'employee',
            function (
                Builder $employeeQuery
            ) use ($filters) {
                $employeeQuery->where(
                    'is_active',
                    true
                );

                $this->applyEmployeeFilters(
                    $employeeQuery,
                    $filters
                );
            }
        );
    }

    private function safeBatchErrorMessage(
        Throwable $e
    ): string {
        if (
            $e
            instanceof
            QueryException
        ) {
            $mysqlError =
                (int) (
                    $e->errorInfo[1]
                    ?? 0
                );

            if (
                in_array(
                    $mysqlError,
                    [
                        1205,
                        1213,
                    ],
                    true
                )
            ) {
                return
                    'Akun sedang terkunci oleh proses lain.';
            }

            if (
                $mysqlError
                ===
                1062
            ) {
                return
                    'ID Login bentrok dengan akun lain.';
            }
        }

        return
            'Reset akun gagal.';
    }


    private function authorizeMaster(
        Request $request
    ): void {
        $email = strtolower(
            trim(
                (string) (
                    $request->user()?->email
                    ?? ''
                )
            )
        );

        abort_unless(
            $email === self::MASTER_EMAIL,
            403
        );
    }
}
