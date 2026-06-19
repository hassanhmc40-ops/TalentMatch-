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

test('ConversationalAgent accepts setContext and returns self', function () {
    $agent = new ConversationalAgent;
    $result = $agent->setContext(['candidate_name' => 'Jane']);

    expect($result)->toBe($agent);
});

test('ConversationalAgent instructions include context block when context is set', function () {
    $context = [
        'candidate_name' => 'Jane Doe',
        'candidat_id' => 42,
        'offer_title' => 'Développeur PHP',
        'offre_id' => 7,
        'analyse_id' => 15,
        'matching_score' => 85,
        'score_level' => 'Excellent',
        'recommendation' => 'Recommandé',
        'key_skills' => ['PHP', 'Laravel', 'SQL'],
    ];

    $instructions = (string) (new ConversationalAgent)->setContext($context)->instructions();

    expect($instructions)->toContain('Contexte actuel');
    expect($instructions)->toContain('Jane Doe');
    expect($instructions)->toContain('Développeur PHP');
    expect($instructions)->toContain('85');
    expect($instructions)->toContain('Excellent');
    expect($instructions)->toContain('Recommandé');
    expect($instructions)->toContain('PHP');
    expect($instructions)->toContain('candidat_id, offre_id, analyse_id');
});

test('ConversationalAgent instructions include answer formatting rules', function () {
    $context = [
        'candidate_name' => 'Jane',
        'candidat_id' => 1,
        'offer_title' => 'Dev',
        'offre_id' => 1,
        'analyse_id' => 1,
        'matching_score' => 75,
        'score_level' => 'Bon',
        'recommendation' => 'Recommandé',
        'key_skills' => ['PHP'],
    ];

    $instructions = (string) (new ConversationalAgent)->setContext($context)->instructions();

    expect($instructions)->toContain('Format des réponses');
    expect($instructions)->toContain('nombre exact');
    expect($instructions)->toContain('puces');
    expect($instructions)->toContain('tableau comparatif');
});

test('ConversationalAgent includes follow-up handling rule', function () {
    $instructions = (string) (new ConversationalAgent)->instructions();

    expect($instructions)->toContain('suivi');
});

test('ConversationalAgent includes out-of-scope handling rule', function () {
    $instructions = (string) (new ConversationalAgent)->instructions();

    expect($instructions)->toContain('hors sujet');
});
