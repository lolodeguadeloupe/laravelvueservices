<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegistrationStep3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => 'required|string|min:8|confirmed',
            'identity_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'professional_insurance' => 'nullable|file|mimes:pdf|max:5120',
            'certifications_files' => 'nullable|array',
            'certifications_files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'identity_document.required' => 'La pièce d\'identité est obligatoire.',
            'identity_document.file' => 'La pièce d\'identité doit être un fichier.',
            'identity_document.mimes' => 'La pièce d\'identité doit être au format PDF, JPG, JPEG ou PNG.',
            'identity_document.max' => 'La pièce d\'identité ne peut pas dépasser 5 MB.',
            'professional_insurance.file' => 'L\'assurance professionnelle doit être un fichier.',
            'professional_insurance.mimes' => 'L\'assurance professionnelle doit être au format PDF.',
            'professional_insurance.max' => 'L\'assurance professionnelle ne peut pas dépasser 5 MB.',
            'certifications_files.array' => 'Les fichiers de certifications doivent être au format liste.',
            'certifications_files.*.file' => 'Chaque certification doit être un fichier.',
            'certifications_files.*.mimes' => 'Chaque certification doit être au format PDF, JPG, JPEG ou PNG.',
            'certifications_files.*.max' => 'Chaque certification ne peut pas dépasser 5 MB.',
            'terms_accepted.required' => 'Vous devez accepter les conditions d\'utilisation.',
            'terms_accepted.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }
}
