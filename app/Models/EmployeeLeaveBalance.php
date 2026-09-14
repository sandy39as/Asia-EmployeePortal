<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveBalance extends Model
{
    protected $fillable = [
        'employee_id',
        'year',
        'entitlement',
        'used',
        'remaining',
    ];

    public function employee()
    {
        return $this->belongsTo(
            Employee::class
        );
    }
}
