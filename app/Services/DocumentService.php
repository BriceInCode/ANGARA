<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function uploadOrUpdateDocument(Request $request, $file, $checksum)
    {
        $existingDocument = $request->documents()->first();

        if ($existingDocument) {
            $this->deleteDocument($existingDocument);
        }

        $filePath = $this->storeDocument($file);

        $document = Document::create([
            'request_id' => $request->id,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'checksum' => $checksum,
        ]);

        return $document;
    }

    private function storeDocument($file)
    {
        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('documents', $fileName, 'public');
    }

    public function deleteDocument(Document $document)
    {
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }
        $document->delete();
    }

    public function verifyChecksum($filePath, $checksum)
    {
        return hash_file('sha256', $filePath) === $checksum;
    }
}
