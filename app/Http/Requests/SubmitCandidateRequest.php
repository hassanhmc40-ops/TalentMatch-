<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitCandidateRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:255'],
            'cv_text' => ['required', 'string', 'min:1', 'max:50000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom' => trim($this->nom ?? ''),
            'cv_text' => trim($this->cv_text ?? ''),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du candidat est obligatoire.',
            'nom.max' => 'Le nom du candidat ne doit pas dépasser 255 caractères.',
            'cv_text.required' => 'Le texte du CV est obligatoire.',
            'cv_text.max' => 'Le texte du CV ne doit pas dépasser 50000 caractères.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom du candidat',
            'cv_text' => 'texte du CV',
        ];
    }
}
