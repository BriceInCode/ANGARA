<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class StoreDocumentRequest extends FormRequest
{
    /**
     * @OA\Schema(
     *   schema="StoreDocumentRequest",
     *   type="object",
     *   required={"request_id", "file_path", "file_type", "file_size", "checksum"},
     *   @OA\Property(property="request_id", type="integer"),
     *   @OA\Property(property="file_path", type="string"),
     *   @OA\Property(property="file_type", type="string", enum={"pdf", "docx", "jpg", "png", "jpeg"}),
     *   @OA\Property(property="file_size", type="integer"),
     *   @OA\Property(property="checksum", type="string", format="sha256")
     * )
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'request_id' => 'required|exists:requests,id',
            'file_path' => 'required|string|max:255',
            'file_type' => ['required', 'string', 'max:255', Rule::in(['pdf', 'docx', 'jpg', 'png', 'jpeg'])],
            'file_size' => 'required|integer|min:1|max:5242880',
            'checksum' => 'required|string|size:64',
        ];
    }

    public function messages()
    {
        return [
            'request_id.required' => 'La référence de la demande est requise.',
            'request_id.exists' => 'La demande spécifiée n\'existe pas.',
            'file_path.required' => 'Le chemin du fichier est requis.',
            'file_type.required' => 'Le type de fichier est requis.',
            'file_type.in' => 'Le type de fichier doit être l\'un des suivants : pdf, docx, jpg, png, jpeg.',
            'file_size.required' => 'La taille du fichier est requise.',
            'file_size.integer' => 'La taille du fichier doit être un nombre entier.',
            'file_size.min' => 'La taille du fichier doit être supérieure à 0.',
            'file_size.max' => 'La taille du fichier ne peut pas dépasser 5 Mo.',
            'checksum.required' => 'Le checksum est requis.',
            'checksum.string' => 'Le checksum doit être une chaîne de caractères.',
            'checksum.size' => 'Le checksum doit avoir une longueur exacte de 64 caractères, correspondant à un hachage SHA-256.',
        ];
    }
}
