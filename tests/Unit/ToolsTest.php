<?php

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Contracts\Tool;

test('GetCandidateAnalysis is a valid tool', function () {
    $tool = new GetCandidateAnalysis;
    expect($tool)->toBeInstanceOf(Tool::class);
    expect((string) $tool->description())->not->toBeEmpty();
});

test('GetCandidateAnalysis schema expects candidat_id', function () {
    $tool = new GetCandidateAnalysis;
    $schema = new JsonSchemaTypeFactory;
    $definition = $tool->schema($schema);

    expect($definition)->toHaveKey('candidat_id');
});

test('GetJobRequirements is a valid tool', function () {
    $tool = new GetJobRequirements;
    expect($tool)->toBeInstanceOf(Tool::class);
    expect((string) $tool->description())->not->toBeEmpty();
});

test('GetJobRequirements schema expects offre_id', function () {
    $tool = new GetJobRequirements;
    $schema = new JsonSchemaTypeFactory;
    $definition = $tool->schema($schema);

    expect($definition)->toHaveKey('offre_id');
});

test('CompareCandidates is a valid tool', function () {
    $tool = new CompareCandidates;
    expect($tool)->toBeInstanceOf(Tool::class);
    expect((string) $tool->description())->not->toBeEmpty();
});

test('CompareCandidates schema expects two analysis IDs', function () {
    $tool = new CompareCandidates;
    $schema = new JsonSchemaTypeFactory;
    $definition = $tool->schema($schema);

    expect($definition)->toHaveKey('analyse_id_1');
    expect($definition)->toHaveKey('analyse_id_2');
});
