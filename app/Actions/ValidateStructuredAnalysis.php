<?php

namespace App\Actions;

use App\Exceptions\ValidationFailedException;
use Illuminate\Support\Facades\Log;

class ValidateStructuredAnalysis
{
    private const int MAX_STRING_LENGTH = 5000;

    private const int MAX_YEARS_EXPERIENCE = 50;

    private const array FRENCH_TO_ENGLISH = [
        'competences_extraites' => 'extracted_skills',
        'annees_experience' => 'years_experience',
        'niveau_etudes' => 'education_level',
        'langues' => 'languages',
        'matching_score' => 'matching_score',
        'points_forts' => 'strengths',
        'lacunes' => 'gaps',
        'competences_manquantes' => 'missing_skills',
        'recommandation' => 'recommendation',
        'justification' => 'justification',
    ];

    private const array VALID_RECOMMENDATIONS = ['convoquer', 'attente', 'rejeter'];

    public function getKeyMapping(): array
    {
        return self::FRENCH_TO_ENGLISH;
    }

    public function validate(array $data): array
    {
        $errors = [];

        $requiredFields = array_keys(self::FRENCH_TO_ENGLISH);

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $data)) {
                $errors[$field] = "Le champ « {$field} » est manquant dans la réponse IA.";
            }
        }

        if ($errors !== []) {
            $this->logError($data, $errors);
            throw new ValidationFailedException($errors);
        }

        $this->validateStringField($data, 'niveau_etudes', $errors);
        $this->validateStringField($data, 'justification', $errors);
        $this->validateStringArrayField($data, 'competences_extraites', $errors);
        $this->validateStringArrayField($data, 'langues', $errors, false);
        $this->validateStringArrayField($data, 'points_forts', $errors);
        $this->validateStringArrayField($data, 'lacunes', $errors);
        $this->validateStringArrayField($data, 'competences_manquantes', $errors, false);
        $this->validateIntegerRange($data, 'annees_experience', 0, self::MAX_YEARS_EXPERIENCE, $errors);
        $this->validateIntegerRange($data, 'matching_score', 0, 100, $errors);
        $this->validateRecommendation($data, $errors);

        if ($errors !== []) {
            $this->logError($data, $errors);
            throw new ValidationFailedException($errors);
        }

        return $this->mapToEnglishKeys($data);
    }

    private function validateStringField(array $data, string $field, array &$errors): void
    {
        $value = $data[$field];

        if (! is_string($value)) {
            $errors[$field] = "Le champ « {$field} » doit être une chaîne de caractères.";

            return;
        }

        if ($value === '') {
            $errors[$field] = "Le champ « {$field} » ne peut pas être vide.";
        }

        if (mb_strlen($value) > self::MAX_STRING_LENGTH) {
            $errors[$field] = "Le champ « {$field} » ne doit pas dépasser ".self::MAX_STRING_LENGTH.' caractères.';
        }
    }

    private function validateStringArrayField(array $data, string $field, array &$errors, bool $required = true): void
    {
        $value = $data[$field];

        if (! is_array($value)) {
            $errors[$field] = "Le champ « {$field} » doit être un tableau.";

            return;
        }

        if ($required && $value === []) {
            $errors[$field] = "Le champ « {$field} » ne peut pas être vide.";

            return;
        }

        foreach ($value as $index => $item) {
            if (! is_string($item)) {
                $errors[$field] = "Le champ « {$field} » doit contenir uniquement des chaînes de caractères (élément {$index} invalide).";

                return;
            }
        }
    }

    private function validateIntegerRange(array $data, string $field, int $min, int $max, array &$errors): void
    {
        $value = $data[$field];

        if (! is_int($value)) {
            $errors[$field] = "Le champ « {$field} » doit être un nombre entier.";

            return;
        }

        if ($value < $min || $value > $max) {
            $errors[$field] = "Le champ « {$field} » doit être compris entre {$min} et {$max}.";
        }
    }

    private function validateRecommendation(array $data, array &$errors): void
    {
        $value = $data['recommandation'];

        if (! is_string($value)) {
            $errors['recommandation'] = 'La recommandation doit être une chaîne de caractères.';

            return;
        }

        if (! in_array($value, self::VALID_RECOMMENDATIONS, true)) {
            $errors['recommandation'] = 'La recommandation doit être l\'une des valeurs suivantes : convoquer, attente, rejeter.';
        }
    }

    private function logError(array $data, array $errors): void
    {
        $truncated = mb_substr(json_encode($data), 0, 500);
        Log::error('Échec de validation de l\'analyse IA structurée.', [
            'errors' => $errors,
            'response_preview' => $truncated,
        ]);
    }

    private function mapToEnglishKeys(array $data): array
    {
        $mapped = [];

        foreach ($data as $frenchKey => $value) {
            $englishKey = self::FRENCH_TO_ENGLISH[$frenchKey] ?? $frenchKey;
            $mapped[$englishKey] = $value;
        }

        return $mapped;
    }
}
