<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegistrationStep1Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            'last_name.required' => 'Le nom de famille est obligatoire.',
            'last_name.max' => 'Le nom de famille ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'date_of_birth.required' => 'La date de naissance est obligatoire.',
            'date_of_birth.date' => 'Veuillez entrer une date valide.',
            'date_of_birth.before' => 'Vous devez être né avant aujourd\'hui.',
            'address.required' => 'L\'adresse est obligatoire.',
            'address.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
        ];
    }
}
