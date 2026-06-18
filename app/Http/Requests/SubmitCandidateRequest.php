<?php

namespace App\Http\Requests;

use App\Models\Candidate;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
            'offre_id' => ['required', 'integer', 'exists:job_offers,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom' => trim($this->nom ?? ''),
            'cv_text' => trim($this->cv_text ?? ''),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $name = Str::lower(trim($this->nom ?? ''));

            if ($name === '') {
                return;
            }

            $exists = Candidate::whereRaw('LOWER(TRIM(name)) = ?', [$name])->exists();

            if ($exists) {
                $validator->errors()->add('nom', 'Ce candidat a déjà été soumis pour cette offre.');
            }
        });
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
            'offre_id.required' => "L'offre d'emploi est requise.",
            'offre_id.exists' => "L'offre d'emploi sélectionnée est invalide.",
            'offre_id.integer' => "L'offre d'emploi doit être un nombre entier.",
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
            'offre_id' => 'offre d\'emploi',
        ];
    }
}
