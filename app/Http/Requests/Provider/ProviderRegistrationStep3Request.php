<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegistrationStep3Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Documents obligatoires
            'identity_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'professional_license' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'insurance_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            // Informations bancaires pour les paiements futurs
            'bank_account_holder' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'size:27', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[0-9]{7}([A-Z0-9]?){0,16}$/'],
            'bic' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],

            // Assurances et garanties
            'has_professional_insurance' => ['required', 'boolean'],
            'insurance_amount' => ['required_if:has_professional_insurance,true', 'nullable', 'numeric', 'min:10000'],
            'insurance_company' => ['required_if:has_professional_insurance,true', 'nullable', 'string', 'max:255'],
            'insurance_expiry_date' => ['required_if:has_professional_insurance,true', 'nullable', 'date', 'after:today'],

            // Déclarations
            'has_criminal_record' => ['required', 'boolean'],
            'criminal_record_details' => ['required_if:has_criminal_record,true', 'nullable', 'string', 'max:1000'],

            // Consentements
            'background_check_consent' => ['required', 'accepted'],
            'data_processing_consent' => ['required', 'accepted'],
            'marketing_consent' => ['nullable', 'boolean'],

            // Informations complémentaires
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'identity_document' => 'pièce d\'identité',
            'professional_license' => 'licence professionnelle',
            'insurance_certificate' => 'certificat d\'assurance',
            'bank_account_holder' => 'titulaire du compte bancaire',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'has_professional_insurance' => 'assurance professionnelle',
            'insurance_amount' => 'montant de l\'assurance',
            'insurance_company' => 'compagnie d\'assurance',
            'insurance_expiry_date' => 'date d\'expiration de l\'assurance',
            'has_criminal_record' => 'casier judiciaire',
            'criminal_record_details' => 'détails du casier judiciaire',
            'background_check_consent' => 'consentement vérification d\'antécédents',
            'data_processing_consent' => 'consentement traitement des données',
            'marketing_consent' => 'consentement marketing',
            'emergency_contact_name' => 'nom du contact d\'urgence',
            'emergency_contact_phone' => 'téléphone du contact d\'urgence',
            'emergency_contact_relation' => 'relation avec le contact d\'urgence',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'identity_document.required' => 'La pièce d\'identité est obligatoire.',
            'identity_document.mimes' => 'La pièce d\'identité doit être au format PDF, JPG, JPEG ou PNG.',
            'identity_document.max' => 'La pièce d\'identité ne doit pas dépasser 5 MB.',

            'insurance_certificate.required' => 'Le certificat d\'assurance est obligatoire.',
            'insurance_certificate.mimes' => 'Le certificat d\'assurance doit être au format PDF, JPG, JPEG ou PNG.',
            'insurance_certificate.max' => 'Le certificat d\'assurance ne doit pas dépasser 5 MB.',

            'professional_license.mimes' => 'La licence professionnelle doit être au format PDF, JPG, JPEG ou PNG.',
            'professional_license.max' => 'La licence professionnelle ne doit pas dépasser 5 MB.',

            'iban.size' => 'L\'IBAN doit contenir exactement 27 caractères.',
            'iban.regex' => 'Le format de l\'IBAN n\'est pas valide.',

            'bic.size' => 'Le BIC doit contenir exactement 11 caractères.',
            'bic.regex' => 'Le format du BIC n\'est pas valide.',

            'insurance_amount.min' => 'Le montant de l\'assurance doit être d\'au moins 10 000€.',
            'insurance_expiry_date.after' => 'L\'assurance doit être valide au-delà d\'aujourd\'hui.',

            'background_check_consent.accepted' => 'Vous devez accepter la vérification d\'antécédents.',
            'data_processing_consent.accepted' => 'Vous devez accepter le traitement des données.',
        ];
    }
}
