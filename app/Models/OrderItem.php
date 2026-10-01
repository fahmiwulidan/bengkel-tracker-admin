<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'name', 'price', 'qty'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
