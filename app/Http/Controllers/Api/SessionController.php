<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Services\SessionService;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SessionController extends Controller
{

    public function createSession(StoreSessionRequest $request)
    {
        try {
            $session = SessionService::createSession($request->validated());
            $user = $session->user;
            $token = $user->createToken('ANGARA-AUTHENTIC')->plainTextToken;

            return Response::json([
                'message' => 'Session créée avec succès.',
                'session' => $session,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function validateOTP(Request $request, $sessionId)
    {
        try {
            $session = SessionService::validateOTP($sessionId, $request->otp);
            return Response::json([
                'message' => 'OTP validé avec succès.',
                'session' => $session
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function resendOTP(Request $request, $sessionId)
    {
        try {
            $otp = SessionService::resendOTP($sessionId);
            return Response::json([
                'message' => 'OTP renvoyé avec succès.',
                'otp' => $otp
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($sessionId)
    {
        $session = Session::find($sessionId);

        if ($session) {
            return Response::json([
                'session' => $session
            ]);
        }

        return Response::json([
            'message' => 'Session introuvable.'
        ], 404);
    }

    public function index()
    {
        $user = Auth::user();
        $sessions = $user->sessions;

        return Response::json([
            'sessions' => $sessions
        ]);
    }

    public function update(StoreSessionRequest $request, $sessionId)
    {
        $session = Session::find($sessionId);

        if (!$session) {
            return Response::json([
                'message' => 'Session introuvable.'
            ], 404);
        }

        $session->update($request->validated());

        return Response::json([
            'message' => 'Session mise à jour avec succès.',
            'session' => $session
        ]);
    }

    public function destroy($sessionId)
    {
        $session = Session::find($sessionId);

        if (!$session) {
            return Response::json([
                'message' => 'Session introuvable.'
            ], 404);
        }

        $session->delete();

        return Response::json([
            'message' => 'Session supprimée avec succès.'
        ]);
    }
}
