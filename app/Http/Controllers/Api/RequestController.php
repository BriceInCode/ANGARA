<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Services\RequestService;
use Illuminate\Support\Facades\Response;


/**
 * @OA\Info(title="Mon API", version="1.0.0")
 *
 * @OA\Schema(
 *     schema="Request",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=123),
 *     @OA\Property(property="details", type="string", example="Demande de service XYZ"),
 *     @OA\Property(property="status", type="string", example="pending")
 * )
 */
class RequestController extends Controller
{
    protected $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->middleware('auth:sanctum');
        $this->requestService = $requestService;
    }

    /**
     * @OA\Post(
     *     path="/api/request",
     *     summary="Créer une demande",
     *     tags={"Request"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "details"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="details", type="string", example="Demande de service XYZ")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Demande créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande créée avec succès."),
     *             @OA\Property(property="request", type="object", ref="#/components/schemas/Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les champs sont requis.")
     *         )
     *     )
     * )
     */
    public function createRequest(StoreRequest $request)
    {
        try {
            $newRequest = $this->requestService->createRequest($request->validated());

            return Response::json([
                'message' => 'Demande créée avec succès.',
                'request' => $newRequest
            ], 201);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/request/{id}",
     *     summary="Mettre à jour une demande",
     *     tags={"Request"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"details"},
     *             @OA\Property(property="details", type="string", example="Mise à jour de la demande XYZ")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande mise à jour avec succès."),
     *             @OA\Property(property="request", type="object", ref="#/components/schemas/Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur lors de la mise à jour de la demande.")
     *         )
     *     )
     * )
     */
    public function updateRequest(StoreRequest $request, $id)
    {
        try {
            $updatedRequest = $this->requestService->updateRequest($id, $request->validated());

            return Response::json([
                'message' => 'Demande mise à jour avec succès.',
                'request' => $updatedRequest
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/request/{id}",
     *     summary="Afficher une demande par ID",
     *     tags={"Request"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="request", type="object", ref="#/components/schemas/Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Demande non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande introuvable.")
     *         )
     *     )
     * )
     */
    public function showRequest($id)
    {
        try {
            $request = $this->requestService->getRequestById($id);

            return Response::json([
                'request' => $request
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/requests",
     *     summary="Liste des demandes",
     *     tags={"Request"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des demandes",
     *         @OA\JsonContent(
     *             @OA\Property(property="requests", type="array", @OA\Items(ref="#/components/schemas/Request"))
     *         )
     *     )
     * )
     */
    public function listRequests()
    {
        try {
            $requests = $this->requestService->listRequests();

            return Response::json([
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/request/{id}",
     *     summary="Supprimer une demande",
     *     tags={"Request"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande supprimée avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de suppression",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur lors de la suppression de la demande.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Demande non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande introuvable.")
     *         )
     *     )
     * )
     */
    public function deleteRequest($id)
    {
        try {
            $this->requestService->deleteRequest($id);

            return Response::json([
                'message' => 'Demande supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/request/session/{sessionId}",
     *     summary="Obtenir une demande par session ID",
     *     tags={"Request"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="request", type="object", ref="#/components/schemas/Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Demande non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande introuvable.")
     *         )
     *     )
     * )
     */
    public function getRequestBySessionId($sessionId)
    {
        try {
            $request = $this->requestService->getRequestBySessionId($sessionId);

            return Response::json([
                'request' => $request
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/request/number/{requestNumber}",
     *     summary="Obtenir une demande par numéro de demande",
     *     tags={"Request"},
     *     @OA\Parameter(
     *         name="requestNumber",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="request", type="object", ref="#/components/schemas/Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Demande non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande introuvable.")
     *         )
     *     )
     * )
     */
    public function getRequestByRequestNumber($requestNumber)
    {
        try {
            $request = $this->requestService->getRequestByRequestNumber($requestNumber);

            return Response::json([
                'request' => $request
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
