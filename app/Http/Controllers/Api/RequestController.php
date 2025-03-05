<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Services\RequestService;
use Illuminate\Support\Facades\Response;

class RequestController extends Controller
{
    protected $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->middleware('auth:sanctum');
        $this->requestService = $requestService;
    }

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
