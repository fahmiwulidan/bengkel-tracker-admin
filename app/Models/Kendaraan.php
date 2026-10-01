<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;
    protected $table = 'kendaraans';
    protected $fillable = [
        'user_id',
        'type',
        'brand',
        'color',
    ];

    /**
     * Relasi ke model User (Setiap kendaraan dimiliki oleh satu User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
