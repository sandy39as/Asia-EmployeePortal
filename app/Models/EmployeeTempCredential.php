<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTempCredential extends Model
{
    protected $fillable = [
        'employee_id',
        'user_id',
        'password_encrypted',
        'created_by',
        'generated_at',
        'exported_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'password_encrypted' => 'encrypted',
            'generated_at' => 'datetime',
            'exported_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
