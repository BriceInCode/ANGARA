<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Document",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="request_id", type="integer", example=123),
 *     @OA\Property(property="file_path", type="string", example="path/to/file.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-05T14:15:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-05T14:15:00Z")
 * )
 */
class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->middleware('auth:sanctum');
        $this->documentService = $documentService;
    }

    /**
     * @OA\Post(
     *     path="/api/document/upload-or-update",
     *     summary="Télécharger ou mettre à jour un document",
     *     tags={"Document"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"file", "request_id", "checksum"},
     *             @OA\Property(property="file", type="string", format="binary"),
     *             @OA\Property(property="checksum", type="string", example="abcdef12345"),
     *             @OA\Property(property="request_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Document téléchargé/mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Document téléchargé/mis à jour avec succès."),
     *             @OA\Property(property="document", ref="#/components/schemas/Document")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors de l'upload ou de la mise à jour du document",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur : [message d'erreur]")
     *         )
     *     )
     * )
     */
    public function uploadOrUpdateDocument(StoreDocumentRequest $request)
    {
        try {
            $file = $request->file('file');
            $checksum = $request->input('checksum');
            $requestId = $request->input('request_id');
            $requestModel = \App\Models\Request::findOrFail($requestId);

            $document = $this->documentService->uploadOrUpdateDocument($requestModel, $file, $checksum);

            return Response::json([
                'message' => 'Document téléchargé/mis à jour avec succès.',
                'document' => $document
            ], 201);
        } catch (\Exception $e) {
            return Response::json([
                'message' => 'Erreur : ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/document/{documentId}/verify-checksum",
     *     summary="Vérifier le checksum du document",
     *     tags={"Document"},
     *     @OA\Parameter(
     *         name="documentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"checksum"},
     *             @OA\Property(property="checksum", type="string", example="abcdef12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Checksum validé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Le checksum est valide."),
     *             @OA\Property(property="valid", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Checksum invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Le checksum est invalide."),
     *             @OA\Property(property="valid", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Document introuvable.")
     *         )
     *     )
     * )
     */
    public function verifyChecksum(Request $request, $documentId)
    {
        try {
            $document = \App\Models\Document::findOrFail($documentId);
            $checksum = $request->input('checksum');

            $isValid = $this->documentService->verifyChecksum(storage_path('app/public/' . $document->file_path), $checksum);

            if ($isValid) {
                return Response::json([
                    'message' => 'Le checksum est valide.',
                    'valid' => true
                ]);
            } else {
                return Response::json([
                    'message' => 'Le checksum est invalide.',
                    'valid' => false
                ], 400);
            }
        } catch (\Exception $e) {
            return Response::json([
                'message' => 'Erreur : ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/document/{documentId}",
     *     summary="Supprimer un document",
     *     tags={"Document"},
     *     @OA\Parameter(
     *         name="documentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Document supprimé avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors de la suppression du document",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur : [message d'erreur]")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Document introuvable.")
     *         )
     *     )
     * )
     */
    public function deleteDocument($documentId)
    {
        try {
            $document = \App\Models\Document::findOrFail($documentId);

            $this->documentService->deleteDocument($document);

            return Response::json([
                'message' => 'Document supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'message' => 'Erreur : ' . $e->getMessage()
            ], 400);
        }
    }
}
