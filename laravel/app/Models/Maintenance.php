<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $table = 'maintenances';

    protected $guarded = [];

    public const UPDATED_AT = null;

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
