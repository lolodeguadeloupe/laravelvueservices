<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBadgeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:badges,name',
            'description' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'type' => 'required|in:achievement,certification,milestone,quality',
            'rarity' => 'required|in:common,rare,epic,legendary',
            'points' => 'required|integer|min:0|max:1000',
            'is_automatic' => 'boolean',
            'is_active' => 'boolean',
            'criteria' => 'nullable|array',
            'criteria.*.field' => 'required_with:criteria|string',
            'criteria.*.value' => 'required_with:criteria|numeric',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du badge est requis.',
            'name.unique' => 'Un badge avec ce nom existe déjà.',
            'description.required' => 'La description est requise.',
            'type.required' => 'Le type de badge est requis.',
            'type.in' => 'Le type de badge doit être : achievement, certification, milestone ou quality.',
            'rarity.required' => 'La rareté est requise.',
            'rarity.in' => 'La rareté doit être : common, rare, epic ou legendary.',
            'points.required' => 'Les points de réputation sont requis.',
            'points.min' => 'Les points doivent être un nombre positif.',
            'points.max' => 'Les points ne peuvent pas dépasser 1000.',
        ];
    }
}
