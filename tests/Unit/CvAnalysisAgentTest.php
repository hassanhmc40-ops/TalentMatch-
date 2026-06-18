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
