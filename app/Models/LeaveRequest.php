<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'employee_id',
        'jenis',
        'durasi_type',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'alasan',
        'lampiran_path',
        'lampiran_original_name',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'local_synced_at',
        'local_sync_status',
        'external_approved_by_name',
        'external_rejected_by_name',
        'kabag_user_id',
        'kabag_status',
        'kabag_approved_by',
        'kabag_approved_at',
        'kabag_rejected_by',
        'kabag_rejected_at',
        'kabag_rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'local_synced_at' => 'datetime',
            'kabag_approved_at' => 'datetime',
            'kabag_rejected_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'rejected_by'
        );
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'izin' => 'Izin',
            'cuti' => 'Cuti',
            'sakit' => 'Sakit',
            default => ucfirst($this->jenis),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function kabag()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'kabag_user_id'
        );
    }

    public function kabagApprovedBy()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'kabag_approved_by'
        );
    }

    public function kabagRejectedBy()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'kabag_rejected_by'
        );
    }

}
