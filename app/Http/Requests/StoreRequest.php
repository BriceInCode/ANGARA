<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\GenderType;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class StoreRequest extends FormRequest
{
    /**
     * @OA\Schema(
     *   schema="StoreRequest",
     *   type="object",
     *   required={"session_id", "request_number", "document_type", "request_reason", "civil_center_reference", "birth_act_number", "birth_act_creation_date", "declaration_by", "first_name", "last_name", "gender", "birth_date", "birth_place", "father_name", "mother_name"},
     *   @OA\Property(property="session_id", type="integer"),
     *   @OA\Property(property="request_number", type="string"),
     *   @OA\Property(property="document_type", type="string"),
     *   @OA\Property(property="request_reason", type="string"),
     *   @OA\Property(property="civil_center_reference", type="string"),
     *   @OA\Property(property="birth_act_number", type="string"),
     *   @OA\Property(property="birth_act_creation_date", type="string", format="date"),
     *   @OA\Property(property="declaration_by", type="string"),
     *   @OA\Property(property="authorized_by", type="string"),
     *   @OA\Property(property="first_name", type="string"),
     *   @OA\Property(property="last_name", type="string"),
     *   @OA\Property(property="gender", type="string", enum={"MASCULIN", "FEMININ"}),
     *   @OA\Property(property="birth_date", type="string", format="date"),
     *   @OA\Property(property="birth_place", type="string"),
     *   @OA\Property(property="father_name", type="string"),
     *   @OA\Property(property="father_birth_date", type="string", format="date"),
     *   @OA\Property(property="father_birth_place", type="string"),
     *   @OA\Property(property="father_profession", type="string"),
     *   @OA\Property(property="mother_name", type="string"),
     *   @OA\Property(property="mother_birth_date", type="string", format="date"),
     *   @OA\Property(property="mother_birth_place", type="string"),
     *   @OA\Property(property="mother_profession", type="string")
     * )
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $rules = [
            'session_id' => 'required|exists:sessions,id',
            'request_number' => ['required', 'string', 'max:255', $isUpdate ? Rule::unique('requests')->ignore($this->route('request')) : 'unique:requests'],
            'document_type' => 'required|string|max:255',
            'request_reason' => 'required|string|max:255',
            'civil_center_reference' => 'required|string|max:255',
            'birth_act_number' => 'required|string|max:255',
            'birth_act_creation_date' => 'required|date',
            'declaration_by' => 'required|string|max:255',
            'authorized_by' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255|min:5',
            'last_name' => 'required|string|max:255',
            'gender' => ['required', Rule::in([GenderType::MASCULIN, GenderType::FEMININ])],
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255|min:5',
            'father_name' => 'required|string|max:255|min:5',
            'father_birth_date' => 'nullable|date',
            'father_birth_place' => 'nullable|string|max:255|min:5',
            'father_profession' => 'nullable|string|max:255|min:5',
            'mother_name' => 'required|string|max:255|min:5',
            'mother_birth_date' => 'nullable|date',
            'mother_birth_place' => 'nullable|string|max:255|min:5',
            'mother_profession' => 'nullable|string|max:255|min:5',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'session_id.required' => 'La session est requise.',
            'session_id.exists' => 'La session spécifiée n\'existe pas.',
            'request_number.required' => 'Le numéro de la demande est requis.',
            'request_number.unique' => 'Ce numéro de demande existe déjà.',
            'request_number.string' => 'Le numéro de la demande doit être une chaîne de caractères.',
            'document_type.required' => 'Le type de document est requis.',
            'request_reason.required' => 'La raison de la demande est requise.',
            'civil_center_reference.required' => 'La référence du centre civil est requise.',
            'birth_act_number.required' => 'Le numéro de l\'acte de naissance est requis.',
            'birth_act_creation_date.required' => 'La date de création de l\'acte de naissance est requise.',
            'declaration_by.required' => 'Le nom de la personne ayant fait la déclaration est requis.',
            'authorized_by.required' => 'Le nom de la personne autorisée est requis.',
            'first_name.required' => 'Le prénom est requis.',
            'first_name.min' => 'Le prénom doit comporter au moins 05 caractères.',
            'last_name.required' => 'Le nom de famille est requis.',
            'last_name.min' => 'Le nom de famille doit comporter au moins 05 caractères.',
            'gender.required' => 'Le sexe est requis.',
            'gender.in' => 'Le sexe doit être masculin ou féminin.',
            'birth_date.required' => 'La date de naissance est requise.',
            'birth_place.required' => 'Le lieu de naissance est requis.',
            'birth_place.min' => 'Le lieu de naissance doit comporter au moins 05 caractères.',
            'father_name.required' => 'Le prénom du père est requis.',
            'father_name.min' => 'Le prénom du père doit comporter au moins 05 caractères.',
            'father_birth_date.date' => 'La date de naissance du père doit être une date valide.',
            'father_birth_place.required' => 'Le lieu de naissance du père est requis.',
            'father_birth_place.min' => 'Le lieu de naissance du père doit comporter au moins 05 caractères.',
            'father_profession.required' => 'La profession du père est requise.',
            'father_profession.min' => 'La profession du père doit comporter au moins 05 caractères.',
            'mother_name.required' => 'Le prénom de la mère est requis.',
            'mother_name.min' => 'Le prénom de la mère doit comporter au moins 05 caractères.',
            'mother_birth_date.date' => 'La date de naissance de la mère doit être une date valide.',
            'mother_birth_place.required' => 'Le lieu de naissance de la mère est requis.',
            'mother_birth_place.min' => 'Le lieu de naissance de la mère doit comporter au moins 05 caractères.',
            'mother_profession.required' => 'La profession de la mère est requise.',
            'mother_profession.min' => 'La profession de la mère doit comporter au moins 05 caractères.',
        ];
    }
}
