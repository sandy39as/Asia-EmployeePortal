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
}
