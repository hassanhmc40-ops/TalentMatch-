<?php

use App\Ai\Agents\CvAnalysisAgent;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Illuminate\JsonSchema\Types\IntegerType;
use Laravel\Ai\Contracts\HasStructuredOutput;

test('CvAnalysisAgent implements Agent and HasStructuredOutput', function () {
    $agent = new CvAnalysisAgent;
    expect($agent)->toBeInstanceOf(HasStructuredOutput::class);
});

test('CvAnalysisAgent schema matches the CV analysis contract', function () {
    $agent = new CvAnalysisAgent;
    $schema = new JsonSchemaTypeFactory;
    $definition = $agent->schema($schema);

    expect($definition)->toHaveKeys([
        'competences_extraites',
        'annees_experience',
        'niveau_etudes',
        'langues',
        'matching_score',
        'points_forts',
        'lacunes',
        'competences_manquantes',
        'recommandation',
        'justification',
    ]);

    expect($definition['annees_experience'])->toBeInstanceOf(IntegerType::class);
    expect($definition['matching_score'])->toBeInstanceOf(IntegerType::class);

    $scoreArray = $definition['matching_score']->toArray();
    expect($scoreArray['minimum'] ?? null)->toBe(0);
    expect($scoreArray['maximum'] ?? null)->toBe(100);
});

test('CvAnalysisAgent instructions are set', function () {
    $agent = new CvAnalysisAgent;
    expect((string) $agent->instructions())->not->toBeEmpty();
});

test('CvAnalysisAgent schema recommendation has enum values', function () {
    $agent = new CvAnalysisAgent;
    $schema = new JsonSchemaTypeFactory;
    $definition = $agent->schema($schema);

    $recommendationArray = $definition['recommandation']->toArray();
    expect($recommendationArray['enum'] ?? [])->toEqual(['convoquer', 'attente', 'rejeter']);
});

test('CvAnalysisAgent instructions are in French', function () {
    $agent = new CvAnalysisAgent;

    expect((string) $agent->instructions())->toContain("spécialisé dans l'analyse");
    expect((string) $agent->instructions())->toContain('JSON');
});

test('CvAnalysisAgent schema matching_score has correct range', function () {
    $agent = new CvAnalysisAgent;
    $schema = new JsonSchemaTypeFactory;
    $definition = $agent->schema($schema);

    $scoreArray = $definition['matching_score']->toArray();
    expect($scoreArray['minimum'] ?? null)->toBe(0);
    expect($scoreArray['maximum'] ?? null)->toBe(100);
});

test('instructions contains role definition section', function () {
    $instructions = (string) (new CvAnalysisAgent)->instructions();

    expect($instructions)->toContain('assistant RH');
    expect($instructions)->toContain("spécialisé dans l'analyse de CV");
});

test('instructions contains anti-hallucination clause', function () {
    $instructions = (string) (new CvAnalysisAgent)->instructions();

    expect($instructions)->toContain('Ne pas inventer');
    expect($instructions)->toContain('ne sont pas explicitement mentionnés');
});

test('instructions contains missing-data handling rules', function () {
    $instructions = (string) (new CvAnalysisAgent)->instructions();

    expect($instructions)->toContain('Non spécifié');
    expect($instructions)->toContain('tableau vide');
});

test('instructions mentions skills, experience, languages, education in score logic', function () {
    $instructions = (string) (new CvAnalysisAgent)->instructions();

    expect($instructions)->toContain('compétences');
    expect($instructions)->toContain('expérience');
    expect($instructions)->toContain('langue');
    expect($instructions)->toContain("niveau d'études");
});

test('instructions contains recommendation criteria thresholds', function () {
    $instructions = (string) (new CvAnalysisAgent)->instructions();

    expect($instructions)->toContain('70');
    expect($instructions)->toContain('40');
    expect($instructions)->toContain('convoquer');
    expect($instructions)->toContain('attente');
    expect($instructions)->toContain('rejeter');
});

test('instructions contains field-by-field rules for all schema fields', function () {
    $instructions = (string) (new CvAnalysisAgent)->instructions();

    $fields = [
        'competences_extraites',
        'annees_experience',
        'niveau_etudes',
        'langues',
        'matching_score',
        'points_forts',
        'lacunes',
        'competences_manquantes',
        'recommandation',
        'justification',
    ];

    foreach ($fields as $field) {
        expect($instructions)->toContain($field);
    }
});
