<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'provider';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'short_description' => ['required', 'string', 'max:160'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_type' => ['required', 'in:fixed,hourly'],
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
            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'title.required' => 'Le titre du service est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 50 caractères.',
            'short_description.required' => 'La description courte est obligatoire.',
            'short_description.max' => 'La description courte ne peut pas dépasser 160 caractères.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'price_type.required' => 'Le type de tarification est obligatoire.',
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'provider_id' => $this->user()?->id,
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
