<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceMonitor extends Model
{
    protected $table = 'device_monitors';

    protected $guarded = [];

    public $timestamps = false;

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
