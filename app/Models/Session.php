<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'expires_at', 'status'];

    protected $casts = [
        'status' => SessionStatus::class,
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => SessionStatus::EN_ATTENTE,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    protected static function booted()
    {
        static::creating(function ($session) {
            if ($session->status === SessionStatus::ACTIF) {
                $existingSession = self::where('user_id', $session->user_id)
                    ->where('status', SessionStatus::ACTIF)
                    ->first();
                if ($existingSession) {
                    throw new \Exception("L'utilisateur a déjà une session active.");
                }
            }

            // Définir expires_at à une heure après la création de la session
            $session->expires_at = Carbon::now()->addHour();
        });
    }
}
