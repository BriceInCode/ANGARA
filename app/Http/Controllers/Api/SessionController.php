<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Services\SessionService;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * @OA\Schema(
 *     schema="Session",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=123),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="otp", type="string", example="123456"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-05T14:15:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-05T14:15:00Z")
 * )
 */
class SessionController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->middleware('auth:sanctum')->except(['createSession']);
        $this->sessionService = $sessionService;
    }

    /**
     * @OA\Post(
     *     path="/api/session",
     *     summary="Créer une session",
     *     tags={"Session"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Session créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session créée avec succès."),
     *             @OA\Property(property="session", ref="#/components/schemas/Session"),
     *             @OA\Property(property="token", type="string", example="your-generated-token-here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de création de session",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur lors de la création de la session.")
     *         )
     *     )
     * )
     */
    public function createSession(StoreSessionRequest $request)
    {
        try {
            $session = $this->sessionService::createSession($request->validated());
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

    /**
     * @OA\Post(
     *     path="/api/session/{sessionId}/validate-otp",
     *     summary="Valider le code OTP",
     *     tags={"Session"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp"},
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP validé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP validé avec succès."),
     *             @OA\Property(property="session", ref="#/components/schemas/Session")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur lors de la validation de l'OTP.")
     *         )
     *     )
     * )
     */
    public function validateOTP(Request $request, $sessionId)
    {
        try {
            $session = $this->sessionService::validateOTP($sessionId, $request->otp);
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

    /**
     * @OA\Post(
     *     path="/api/session/{sessionId}/resend-otp",
     *     summary="Renvoyer le code OTP",
     *     tags={"Session"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP renvoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP renvoyé avec succès."),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de renvoi d'OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur lors du renvoi de l'OTP.")
     *         )
     *     )
     * )
     */
    public function resendOTP(Request $request, $sessionId)
    {
        try {
            $otp = $this->sessionService::resendOTP($sessionId);
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

    /**
     * @OA\Get(
     *     path="/api/session/{sessionId}",
     *     summary="Afficher les informations d'une session",
     *     tags={"Session"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Session trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="session", ref="#/components/schemas/Session")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session introuvable.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/sessions",
     *     summary="Lister les sessions de l'utilisateur connecté",
     *     tags={"Session"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des sessions",
     *         @OA\JsonContent(
     *             @OA\Property(property="sessions", type="array", @OA\Items(ref="#/components/schemas/Session"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $sessions = $user->sessions;

        return Response::json([
            'sessions' => $sessions
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/session/{sessionId}",
     *     summary="Mettre à jour une session",
     *     tags={"Session"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Session mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session mise à jour avec succès."),
     *             @OA\Property(property="session", ref="#/components/schemas/Session")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session introuvable.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/session/{sessionId}",
     *     summary="Supprimer une session",
     *     tags={"Session"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Session supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session supprimée avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session introuvable.")
     *         )
     *     )
     * )
     */
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
