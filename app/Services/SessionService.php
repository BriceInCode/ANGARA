<?php

namespace App\Services;

use App\Models\Session;
use App\Models\User;
use App\Services\EmailService;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SessionService
{
    public static function createSession($data)
    {
        $user = User::firstOrCreate(
            ['email' => $data['email'] ?? null, 'phone' => $data['phone'] ?? null],
            ['otp' => null, 'otp_expires_at' => null]
        );

        $activeSession = $user->sessions()->where('status', \App\Enums\SessionStatus::ACTIF)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($activeSession) {
            return $activeSession;
        }

        $session = Session::create([
            'user_id' => $user->id,
            'expires_at' => Carbon::now()->addHour(),
            'status' => \App\Enums\SessionStatus::EN_ATTENTE,
        ]);

        $otp = rand(10000, 99999);
        Cache::put("otp_{$session->id}", $otp, now()->addHour());

        if ($data['email']) {
            EmailService::sendMail($user->email, "Votre code OTP", 'emails.otp', ['otp' => $otp]);
        } elseif ($data['phone']) {
            (new WhatsappService())->sendMessage($user->phone, "Votre code OTP : $otp");
        }

        return $session;
    }

    public static function validateOTP($sessionId, $otp)
    {
        $session = Session::findOrFail($sessionId);

        if (Carbon::now()->greaterThan($session->expires_at)) {
            throw new \Exception("La session a expiré.");
        }

        $attempts = Cache::get("otp_attempts_{$session->id}", 0);
        if ($attempts >= 3) {
            throw new \Exception("Vous avez dépassé le nombre de tentatives autorisées.");
        }

        $validOtp = Cache::get("otp_{$session->id}");
        if ($otp != $validOtp) {
            Cache::put("otp_attempts_{$session->id}", $attempts + 1, now()->addMinutes(10));
            throw new \Exception("Le code OTP est incorrect.");
        }

        $session->update(['status' => \App\Enums\SessionStatus::ACTIF]);

        Cache::forget("otp_{$session->id}");

        return $session;
    }

    public static function resendOTP($sessionId)
    {
        $session = Session::findOrFail($sessionId);

        if (Carbon::now()->greaterThan($session->expires_at)) {
            throw new \Exception("La session a expiré.");
        }

        $attempts = Cache::get("otp_attempts_{$session->id}", 0);
        if ($attempts >= 3) {
            throw new \Exception("Vous avez dépassé le nombre de tentatives autorisées.");
        }

        $otp = rand(10000, 99999);
        Cache::put("otp_{$session->id}", $otp, now()->addHour());
        $user = $session->user;
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addHour();
        $user->save();

        if ($user->email) {
            EmailService::sendMail($user->email, "Votre nouveau code OTP", 'emails.otp', ['otp' => $otp]);
        } elseif ($user->phone) {
            $whatsappService = new WhatsappService();
            $whatsappService->sendMessage($user->phone, "Votre nouveau code OTP : $otp");
        }

        return $otp;
    }
}
