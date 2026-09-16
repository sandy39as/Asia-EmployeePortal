<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeTempCredential;
use Illuminate\Database\Eloquent\Builder;
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

            /*
            |--------------------------------------------------------------------------
            | RESET KARYAWAN TERPILIH
            |--------------------------------------------------------------------------
            */
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

        $area = trim((string) ($validated['area'] ?? ''));
        $category = trim((string) ($validated['category'] ?? ''));
        $search = trim((string) ($validated['search'] ?? ''));
        $allowAll = (bool) ($validated['allow_all'] ?? false);

        $resetMode =
            $validated['reset_mode'];

        $selectedEmployeeIds = collect(
            $validated['employee_ids']
            ?? []
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
            /*
            |--------------------------------------------------------------------------
            | MODE CHECKBOX
            |--------------------------------------------------------------------------
            |
            | Jika ada employee_ids[], HANYA karyawan yang dicentang yang diproses.
            | Filter tidak menambah karyawan lain.
            |
            */
            $employeesQuery->whereIn(
                'id',
                $selectedEmployeeIds->all()
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | MODE FILTER MASSAL
            |--------------------------------------------------------------------------
            */
            $this->applyEmployeeFilters(
                $employeesQuery,
                $filters
            );
        }

        $employees = $employeesQuery->get();

        if ($employees->isEmpty()) {
            return back()->withErrors([
                'mass_reset' =>
                    'Tidak ada akun karyawan yang cocok dengan filter.',
            ]);
        }

        $count = 0;

        DB::transaction(function () use (
            $employees,
            $request,
            $resetMode,
            &$count
        ) {
            foreach ($employees as $employee) {
                $user = $employee->user;

                if (! $user) {
                    continue;
                }

                $plainPassword = (string) random_int(
                    100000,
                    999999
                );

                $userData = [
                    'password' =>
                        Hash::make(
                            $plainPassword
                        ),

                    'must_change_password' =>
                        true,
                ];

                if (
                    $resetMode
                    ===
                    'login_and_password'
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | RESET ID LOGIN KE ID KARYAWAN
                    |--------------------------------------------------------------------------
                    |
                    | Setelah login menggunakan employee_code, user wajib membuat
                    | ID Login / email baru lagi.
                    |
                    */
                    $userData['username'] =
                        $employee->employee_code;

                    $userData['must_change_username'] =
                        true;
                }

                $user->forceFill(
                    $userData
                )->save();

                EmployeeTempCredential::updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    [
                        'employee_id' => $employee->id,
                        'password_encrypted' => $plainPassword,
                        'created_by' => $request->user()->id,
                        'generated_at' => now(),
                        'exported_at' => null,
                        'expires_at' => now()->addDays(7),
                    ]
                );

                $count++;
            }
        });

        $modeLabel =
            $resetSelectedOnly
                ? 'karyawan terpilih'
                : 'hasil filter';

        $resetLabel =
            $resetMode
            ===
            'login_and_password'
                ? 'ID Login + password'
                : 'password';

        return redirect()
            ->route(
                'master.employee-credentials.index',
                array_filter(
                    $filters,
                    fn ($value) => $value !== ''
                )
            )
            ->with(
                'success',
                $count
                . ' akun '
                . $modeLabel
                . ' berhasil direset '
                . $resetLabel
                . '. Password wajib diganti saat login.'
            );
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

        DB::transaction(function () use (
            $employee,
            $request,
            $plainPassword,
            $resetMode
        ) {
            $userData = [
                'password' =>
                    Hash::make(
                        $plainPassword
                    ),

                'must_change_password' =>
                    true,
            ];

            if (
                $resetMode
                ===
                'login_and_password'
            ) {
                $userData['username'] =
                    $employee->employee_code;

                $userData['must_change_username'] =
                    true;
            }

            $employee
                ->user
                ->forceFill(
                    $userData
                )
                ->save();

            EmployeeTempCredential::updateOrCreate(
                [
                    'user_id' => $employee->user->id,
                ],
                [
                    'employee_id' => $employee->id,
                    'password_encrypted' => $plainPassword,
                    'created_by' => $request->user()->id,
                    'generated_at' => now(),
                    'exported_at' => null,
                    'expires_at' => now()->addDays(7),
                ]
            );
        });

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
