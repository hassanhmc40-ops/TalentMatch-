<?php

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;

test('GetCandidateAnalysis implements the Tool contract', function () {
    $tool = new GetCandidateAnalysis;

    expect($tool)->toBeInstanceOf(Tool::class);
    expect(method_exists($tool, 'description'))->toBeTrue();
    expect(method_exists($tool, 'handle'))->toBeTrue();
    expect(method_exists($tool, 'schema'))->toBeTrue();
    expect($tool->description())->toBeString();
    expect($tool->description())->not->toBeEmpty();
});

test('GetJobRequirements implements the Tool contract', function () {
    $tool = new GetJobRequirements;

    expect($tool)->toBeInstanceOf(Tool::class);
    expect(method_exists($tool, 'description'))->toBeTrue();
    expect(method_exists($tool, 'handle'))->toBeTrue();
    expect(method_exists($tool, 'schema'))->toBeTrue();
    expect($tool->description())->toBeString();
    expect($tool->description())->not->toBeEmpty();
});

test('CompareCandidates implements the Tool contract', function () {
    $tool = new CompareCandidates;

    expect($tool)->toBeInstanceOf(Tool::class);
    expect(method_exists($tool, 'description'))->toBeTrue();
    expect(method_exists($tool, 'handle'))->toBeTrue();
    expect(method_exists($tool, 'schema'))->toBeTrue();
    expect($tool->description())->toBeString();
    expect($tool->description())->not->toBeEmpty();
});

test('tools descriptions are in French', function () {
    $getAnalysis = (new GetCandidateAnalysis)->description();
    $getRequirements = (new GetJobRequirements)->description();
    $compare = (new CompareCandidates)->description();

    expect($getAnalysis)->toContain('candidat');
    expect($getRequirements)->toContain('offre');
    expect($compare)->toContain('candidats');
});

test('tools schema method accepts JsonSchema and returns array', function () {
    $returnType = new class
    {
        public function required()
        {
            return null;
        }
    };

    $schema = $this->createMock(JsonSchema::class);
    $schema->method('integer')->willReturn($returnType);

    expect((new GetCandidateAnalysis)->schema($schema))->toBeArray();
    expect((new GetJobRequirements)->schema($schema))->toBeArray();
    expect((new CompareCandidates)->schema($schema))->toBeArray();
});
