<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_karyawan_id',
        'employee_code',
        'nama',
        'pin_fingerspot',
        'source_device_id',
        'jabatan',
        'tanggal_masuk',
        'status_kerja',
        'is_active',
        'last_synced_at',
        'employment_group',
        'source_kategori_karyawan_id',
        'source_kategori_karyawan_name',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function kabag()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'kabag_employee',
            'employee_id',
            'kabag_user_id'
        )
        ->where('users.role', 'kabag')
        ->withTimestamps();
    }

    public function leaveBalances()
    {
        return $this->hasMany(
            \App\Models\EmployeeLeaveBalance::class
        );
    }

    public function leaveBalanceForYear(?int $year = null)
    {
        $year ??= now()->year;

        return $this->leaveBalances()
            ->firstOrCreate(
                [
                    'year' => $year,
                ],
                [
                    'entitlement' => 12,
                    'used' => 0,
                    'remaining' => 12,
                ]
            );
    }

    public function isAsiaEmployee(): bool
    {
        return $this->employment_group === 'asia';
    }

    public function isOutsourcingEmployee(): bool
    {
        return $this->employment_group === 'outsourcing';
    }

    public function leaveBalanceForYear(?int $year = null)
    {
        if (! $this->isAsiaEmployee()) {
            return null;
        }

        $year ??= now()->year;

        return $this->leaveBalances()
            ->firstOrCreate(
                [
                    'year' => $year,
                ],
                [
                    'entitlement' => 12,
                    'used' => 0,
                    'remaining' => 12,
                ]
            );
    }

}
