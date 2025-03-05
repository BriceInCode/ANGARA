<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->middleware('auth:sanctum');
        $this->documentService = $documentService;
    }

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

