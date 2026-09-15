<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'username',
        'email',
        'password',
        'role',
        'must_change_password',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    public function isHrd(): bool
    {
        return in_array(
            $this->role,
            [
                'hrd',
                'admin',
                'superadmin',
            ],
            true
        );
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    public function isKabag(): bool
    {
        return $this->role === 'kabag';
    }

    public function managedEmployees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'kabag_employee',
            'kabag_user_id',
            'employee_id'
        )
            ->withTimestamps();
    }
}
