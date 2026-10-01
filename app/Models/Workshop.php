<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'name',
        'address',
        'rating',
        'is_open',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_open' => 'boolean',
    ];

    public function mechanics()
    {
        return $this->hasMany(Mechanic::class);
    }
}