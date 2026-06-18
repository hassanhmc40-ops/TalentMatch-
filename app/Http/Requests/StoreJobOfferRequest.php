<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'required_skills' => ['required', 'array', 'min:1'],
            'required_skills.*' => ['required', 'string', 'max:100', 'distinct'],
            'min_experience_years' => ['required', 'integer', 'min:0', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'required_skills.required' => 'Au moins une compétence est requise.',
            'required_skills.min' => 'Au moins une compétence est requise.',
            'required_skills.*.distinct' => 'Les compétences doivent être uniques.',
            'required_skills.*.max' => 'Chaque compétence ne doit pas dépasser 100 caractères.',
            'min_experience_years.required' => "L'année d'expérience minimum est requise.",
            'min_experience_years.integer' => "L'année d'expérience doit être un nombre entier.",
            'min_experience_years.min' => "L'année d'expérience minimale doit être 0 ou plus.",
            'min_experience_years.max' => "L'année d'expérience maximale est de 50 ans.",
        ];
    }
}
