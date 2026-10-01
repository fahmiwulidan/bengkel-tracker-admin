<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'user_id',
        'workshop_id',
        'mechanic_id',
        'status',
        'problem',
        'basic_cost',
        'total_cost',
        'eta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }
}