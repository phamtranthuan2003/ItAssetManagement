<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $table = 'devices';

    protected $guarded = [];

    protected $casts = ['is_temporary' => 'boolean'];

    public function specifications()
    {
        return $this->hasOne(DeviceSpec::class);
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(DeviceMonitor::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(DeviceHistory::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
