<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegistrationStep2Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'nullable|string|max:255',
            'siret' => 'nullable|string|max:14',
            'bio' => 'required|string|max:1000',
            'experience' => 'required|string|max:2000',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'bio.required' => 'La description de votre profil est obligatoire.',
            'bio.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'experience.required' => 'La description de votre expérience est obligatoire.',
            'experience.max' => 'La description de l\'expérience ne peut pas dépasser 2000 caractères.',
            'company_name.max' => 'Le nom de l\'entreprise ne peut pas dépasser 255 caractères.',
            'siret.max' => 'Le numéro SIRET ne peut pas dépasser 14 caractères.',
            'certifications.array' => 'Les certifications doivent être au format liste.',
            'certifications.*.string' => 'Chaque certification doit être du texte.',
            'certifications.*.max' => 'Chaque certification ne peut pas dépasser 255 caractères.',
        ];
    }
}
