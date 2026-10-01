<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    // Menonaktifkan updated_at karena di tabel log ERD hanya ada created_at
    const UPDATED_AT = null;

    protected $fillable = ['order_id', 'previous_status', 'current_status', 'information'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
