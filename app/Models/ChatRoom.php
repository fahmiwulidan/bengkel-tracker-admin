<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    protected $fillable = ['order_id'];

    // Relasi ke Order (Satu chat room punya satu order)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Chat Message (Satu chat room berisi banyak pesan)
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
