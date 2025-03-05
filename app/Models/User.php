<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['email', 'phone', 'otp', 'otp_expires_at'];

    protected $casts = ['otp_expires_at' => 'datetime'];

    protected $attributes = ['otp' => null];


    public function activeSession()
    {
        return $this->hasOne(Session::class)->where('status', SessionStatus::ACTIF);
    }
}
