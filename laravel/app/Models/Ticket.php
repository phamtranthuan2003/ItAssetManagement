<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $guarded = [];

    public const UPDATED_AT = null;

    protected $casts = ['is_temporary' => 'boolean'];

    public function details(): HasMany
    {
        return $this->hasMany(TicketDetail::class);
    }
}
