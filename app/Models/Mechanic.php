<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mechanic extends Model
{
    protected $fillable = [
        'workshop_id',
        'name',
        'status',
        'phone',
        'rating',
        'is_available',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_available' => 'boolean',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}