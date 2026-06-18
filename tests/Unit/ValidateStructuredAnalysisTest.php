<?php

use App\Actions\ValidateStructuredAnalysis;
use App\Exceptions\ValidationFailedException;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->validator = new ValidateStructuredAnalysis;
});

test('valid full payload passes validation and maps keys to English', function () {
    $payload = [
        'competences_extraites' => ['PHP', 'Laravel'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français', 'Anglais'],
        'matching_score' => 78,
        'points_forts' => ['Expérience en gestion'],
        'lacunes' => ['Manque de certification'],
        'competences_manquantes' => ['Docker'],
        'recommandation' => 'convoquer',
        'justification' => 'Profil correspond bien aux exigences du poste.',
    ];

    $mapped = $this->validator->validate($payload);

    expect($mapped)->toHaveKeys([
        'extracted_skills', 'years_experience', 'education_level', 'languages',
        'matching_score', 'strengths', 'gaps', 'missing_skills', 'recommendation', 'justification',
    ]);
    expect($mapped['years_experience'])->toBe(5);
    expect($mapped['matching_score'])->toBe(78);
    expect($mapped['recommendation'])->toBe('convoquer');
});

test('missing required field throws exception', function () {
    $payload = [
        'competences_extraites' => ['PHP'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français'],
        'matching_score' => 50,
        'points_forts' => ['Test'],
        'lacunes' => [],
        'competences_manquantes' => [],
        'recommandation' => 'attente',
    ];

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('empty string field is rejected', function () {
    $payload = validAnalysisPayload(['justification' => '']);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('non-string value for string field is rejected', function () {
    $payload = validAnalysisPayload(['niveau_etudes' => 123]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('string exceeding maximum length is rejected', function () {
    $payload = validAnalysisPayload(['justification' => str_repeat('a', 5001)]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('non-array value for array field is rejected', function () {
    $payload = validAnalysisPayload(['competences_extraites' => 'PHP, Laravel']);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('array with non-string items is rejected', function () {
    $payload = validAnalysisPayload(['competences_extraites' => ['PHP', 123, true]]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('empty array for optional fields passes', function () {
    $payload = validAnalysisPayload(['langues' => [], 'competences_manquantes' => []]);

    $mapped = $this->validator->validate($payload);

    expect($mapped['languages'])->toBe([]);
    expect($mapped['missing_skills'])->toBe([]);
});

test('matching_score below 0 is rejected', function () {
    $payload = validAnalysisPayload(['matching_score' => -5]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('matching_score above 100 is rejected', function () {
    $payload = validAnalysisPayload(['matching_score' => 150]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('non-integer matching_score is rejected', function () {
    $payload = validAnalysisPayload(['matching_score' => 78.5]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('years_experience negative is rejected', function () {
    $payload = validAnalysisPayload(['annees_experience' => -1]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('years_experience exceeding 50 is rejected', function () {
    $payload = validAnalysisPayload(['annees_experience' => 99]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('invalid recommendation enum is rejected', function () {
    $payload = validAnalysisPayload(['recommandation' => 'embaucher']);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('non-string recommendation is rejected', function () {
    $payload = validAnalysisPayload(['recommandation' => 123]);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('missing recommandation field throws exception', function () {
    $payload = validAnalysisPayload();
    unset($payload['recommandation']);

    expect(fn () => $this->validator->validate($payload))
        ->toThrow(ValidationFailedException::class);
});

test('exception contains field-level error details', function () {
    $payload = validAnalysisPayload(['matching_score' => -1]);

    try {
        $this->validator->validate($payload);
    } catch (ValidationFailedException $e) {
        expect($e->errors)->toHaveKey('matching_score');
    }
});

test('key mapping returns correct english keys', function () {
    $mapping = $this->validator->getKeyMapping();

    expect($mapping['competences_extraites'])->toBe('extracted_skills');
    expect($mapping['annees_experience'])->toBe('years_experience');
    expect($mapping['niveau_etudes'])->toBe('education_level');
    expect($mapping['langues'])->toBe('languages');
    expect($mapping['matching_score'])->toBe('matching_score');
    expect($mapping['points_forts'])->toBe('strengths');
    expect($mapping['lacunes'])->toBe('gaps');
    expect($mapping['competences_manquantes'])->toBe('missing_skills');
    expect($mapping['recommandation'])->toBe('recommendation');
    expect($mapping['justification'])->toBe('justification');
});

// ── Helpers ──

function validAnalysisPayload(array $overrides = []): array
{
    return array_merge([
        'competences_extraites' => ['PHP', 'Laravel'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français', 'Anglais'],
        'matching_score' => 78,
        'points_forts' => ['Expérience en gestion de projet'],
        'lacunes' => ['Manque de certification'],
        'competences_manquantes' => ['Docker'],
        'recommandation' => 'convoquer',
        'justification' => 'Profil correspond bien aux exigences.',
    ], $overrides);
}
