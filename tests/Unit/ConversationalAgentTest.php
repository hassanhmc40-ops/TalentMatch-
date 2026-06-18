<?php

use App\Ai\Agents\ConversationalAgent;
use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;

test('ConversationalAgent implements Agent and Conversational', function () {
    $agent = new ConversationalAgent;
    expect($agent)->toBeInstanceOf(Conversational::class);
});

test('ConversationalAgent uses RemembersConversations trait', function () {
    $traits = class_uses(ConversationalAgent::class);
    expect($traits)->toContain(RemembersConversations::class);
});

test('ConversationalAgent implements HasTools', function () {
    $agent = new ConversationalAgent;
    expect($agent)->toBeInstanceOf(HasTools::class);
});

test('ConversationalAgent has access to required tools', function () {
    $agent = new ConversationalAgent;
    $tools = iterator_to_array($agent->tools());

    expect($tools)->toHaveCount(3);

    $toolClasses = array_map(fn ($tool) => $tool::class, $tools);
    expect($toolClasses)->toContain(
        GetCandidateAnalysis::class,
        GetJobRequirements::class,
        CompareCandidates::class,
    );
});

test('ConversationalAgent instructions are set', function () {
    $agent = new ConversationalAgent;
    expect((string) $agent->instructions())->not->toBeEmpty();
});

test('ConversationalAgent instructions are in French', function () {
    $instructions = (string) (new ConversationalAgent)->instructions();

    expect($instructions)->toContain('assistant RH');
    expect($instructions)->toContain('recruteurs');
    expect($instructions)->toContain('candidats');
});

test('ConversationalAgent instructions forbid inventing data without tools', function () {
    $instructions = (string) (new ConversationalAgent)->instructions();

    expect($instructions)->toContain('N\'invente jamais');
    expect($instructions)->toContain('outils');
    expect($instructions)->toContain('DOIS utiliser');
});
