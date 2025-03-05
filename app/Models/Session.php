<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'expires_at', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'status' => SessionStatus::class,
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => SessionStatus::EN_ATTENTE,
    ];
}
