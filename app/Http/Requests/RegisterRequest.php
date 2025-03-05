<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required_without:phone|email|unique:users,email',
            'phone' => 'required_without:email|string|unique:users,phone',
        ];
    }

    public function messages()
    {
        return [
            'email.required_without' => 'L\'email est requis si vous ne fournissez pas un numéro de téléphone.',
            'phone.required_without' => 'Le numéro de téléphone est requis si vous ne fournissez pas un email.',
            'email.email' => 'L\'email doit être un format valide.',
            'email.unique' => 'L\'email existe déjà dans notre base de données.',
            'phone.unique' => 'Le numéro de téléphone existe déjà dans notre base de données.',
        ];
    }
}
