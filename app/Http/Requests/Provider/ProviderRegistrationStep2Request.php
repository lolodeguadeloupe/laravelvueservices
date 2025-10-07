<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegistrationStep2Request extends FormRequest
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
            'company_name' => ['nullable', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:50', 'max:1000'],
            'experience' => ['required', 'integer', 'min:0', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', 'max:50'],
            'specialties' => ['required', 'array', 'min:1', 'max:5'],
            'specialties.*' => ['integer', 'exists:categories,id'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:255'],
            'availability_hours' => ['required', 'array'],
            'availability_hours.monday' => ['nullable', 'array'],
            'availability_hours.monday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.monday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.monday.start'],
            'availability_hours.tuesday' => ['nullable', 'array'],
            'availability_hours.tuesday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.tuesday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.tuesday.start'],
            'availability_hours.wednesday' => ['nullable', 'array'],
            'availability_hours.wednesday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.wednesday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.wednesday.start'],
            'availability_hours.thursday' => ['nullable', 'array'],
            'availability_hours.thursday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.thursday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.thursday.start'],
            'availability_hours.friday' => ['nullable', 'array'],
            'availability_hours.friday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.friday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.friday.start'],
            'availability_hours.saturday' => ['nullable', 'array'],
            'availability_hours.saturday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.saturday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.saturday.start'],
            'availability_hours.sunday' => ['nullable', 'array'],
            'availability_hours.sunday.start' => ['nullable', 'string', 'date_format:H:i'],
            'availability_hours.sunday.end' => ['nullable', 'string', 'date_format:H:i', 'after:availability_hours.sunday.start'],
            'intervention_radius' => ['required', 'integer', 'min:1', 'max:100'],
            'accepts_urgent_requests' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'nom de société',
            'bio' => 'description professionnelle',
            'experience' => 'années d\'expérience',
            'address' => 'adresse',
            'city' => 'ville',
            'postal_code' => 'code postal',
            'country' => 'pays',
            'languages' => 'langues parlées',
            'specialties' => 'spécialités',
            'certifications' => 'certifications',
            'availability_hours' => 'horaires de disponibilité',
            'intervention_radius' => 'rayon d\'intervention',
            'accepts_urgent_requests' => 'demandes urgentes',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'bio.min' => 'La description professionnelle doit contenir au moins 50 caractères.',
            'bio.max' => 'La description professionnelle ne peut pas dépasser 1000 caractères.',
            'experience.min' => 'L\'expérience ne peut pas être négative.',
            'experience.max' => 'L\'expérience ne peut pas dépasser 50 ans.',
            'languages.min' => 'Vous devez indiquer au moins une langue parlée.',
            'specialties.min' => 'Vous devez sélectionner au moins une spécialité.',
            'specialties.max' => 'Vous ne pouvez pas sélectionner plus de 5 spécialités.',
            'intervention_radius.min' => 'Le rayon d\'intervention doit être d\'au moins 1 km.',
            'intervention_radius.max' => 'Le rayon d\'intervention ne peut pas dépasser 100 km.',
        ];
    }
}
