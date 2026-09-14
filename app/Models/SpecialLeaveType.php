<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialLeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'default_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialLeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'default_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
