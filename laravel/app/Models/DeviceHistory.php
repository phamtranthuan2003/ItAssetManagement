<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceHistory extends Model
{
    protected $table = 'device_history';

    protected $guarded = [];

    public const UPDATED_AT = null;

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
