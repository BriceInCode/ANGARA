<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SessionStatus;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class StoreSessionRequest extends FormRequest
{
    /**
     * @OA\Schema(
     *   schema="StoreSessionRequest",
     *   type="object",
     *   required={"user_id", "token", "status"},
     *   @OA\Property(property="user_id", type="integer"),
     *   @OA\Property(property="token", type="string"),
     *   @OA\Property(property="status", type="string", enum={"EN_ATTENTE", "ACTIF"}),
     *   @OA\Property(property="expires_at", type="string", format="date-time")
     * )
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'token' => 'required|string|max:255',
            'status' => ['required', Rule::in([SessionStatus::EN_ATTENTE, SessionStatus::ACTIF])],
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'L\'ID de l\'utilisateur est requis.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
            'token.required' => 'Le token est requis.',
            'token.string' => 'Le token doit être une chaîne de caractères.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Il semblerait que votre session ait été révoquée.',
            'expires_at.date' => 'Il semblerait que votre session ait déjà expiré.',
            'expires_at.after' => 'La date d\'expiration doit être dans le futur.',
        ];
    }
}
