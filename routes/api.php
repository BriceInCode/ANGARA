<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SessionController;

Route::post('/session', [SessionController::class, 'createSession']);  // Route pour créer une session (non protégée)

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/session/{sessionId}/validate-otp', [SessionController::class, 'validateOTP']);  // Route pour valider l'OTP
    Route::post('/session/{sessionId}/resend-otp', [SessionController::class, 'resendOTP']);  // Route pour renvoyer l'OTP

    Route::get('/session', [SessionController::class, 'index']);  // Liste toutes les sessions de l'utilisateur
    Route::get('/session/{sessionId}', [SessionController::class, 'show']);  // Afficher une session par ID
    Route::put('/session/{sessionId}', [SessionController::class, 'update']);  // Mettre à jour une session
    Route::delete('/session/{sessionId}', [SessionController::class, 'destroy']);  // Supprimer une session
});
