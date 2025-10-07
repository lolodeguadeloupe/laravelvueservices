<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $service = $this->route('service');

        return $this->user()?->user_type === 'provider'
            && $service?->provider_id === $this->user()?->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'min:50'],
            'short_description' => ['sometimes', 'string', 'max:160'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'price_type' => ['sometimes', 'in:fixed,hourly'],
            'duration' => ['nullable', 'integer', 'min:15'],
            'location' => ['nullable', 'array'],
            'location.address' => ['nullable', 'string', 'max:255'],
            'location.city' => ['nullable', 'string', 'max:100'],
            'location.postal_code' => ['nullable', 'string', 'max:10'],
            'location.radius_km' => ['nullable', 'integer', 'min:1', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.min' => 'La description doit contenir au moins 50 caractères.',
            'short_description.max' => 'La description courte ne peut pas dépasser 160 caractères.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'price_type.in' => 'Le type de tarification doit être "fixe" ou "horaire".',
            'duration.integer' => 'La durée doit être un nombre entier.',
            'duration.min' => 'La durée minimum est de 15 minutes.',
            'images.max' => 'Vous ne pouvez pas télécharger plus de 10 images.',
            'images.*.image' => 'Tous les fichiers doivent être des images.',
            'images.*.mimes' => 'Les images doivent être au format JPEG, JPG, PNG ou WebP.',
            'images.*.max' => 'Chaque image ne peut pas dépasser 2 Mo.',
            'location.radius_km.max' => 'Le rayon d\'intervention ne peut pas dépasser 100 km.',
        ];
    }
}
