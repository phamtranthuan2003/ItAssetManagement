<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'employees';

    protected $guarded = [];

    public const UPDATED_AT = null;

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedDevices(): HasMany
    {
        return $this->hasMany(Device::class, 'current_employee_id');
    }
}
