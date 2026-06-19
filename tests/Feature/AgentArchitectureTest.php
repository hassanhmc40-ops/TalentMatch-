<?php

use App\Ai\Agents\ConversationalAgent;
use App\Models\User;
use Laravel\Ai\Ai;
use Laravel\Ai\Responses\AgentResponse;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

test('agent lifecycle make continue prompt returns AgentResponse', function () {
    Ai::fakeAgent(ConversationalAgent::class, ['Voici l\'analyse du candidat.']);

    $agent = ConversationalAgent::make()
        ->continue('test-conv-1', $this->user);

    $response = $agent->prompt('Parle-moi du candidat');

    expect($response)->toBeInstanceOf(AgentResponse::class);
    expect($response->text)->toBe('Voici l\'analyse du candidat.');
    expect($response->conversationId)->toBe('test-conv-1');
});

test('agent lifecycle make forUser prompt returns AgentResponse', function () {
    Ai::fakeAgent(ConversationalAgent::class, [
        'Bonjour ! Je suis l\'assistant.',  // main response
        'Titre',                              // title generation
    ]);

    $agent = ConversationalAgent::make()
        ->forUser($this->user);

    $response = $agent->prompt('Bonjour');

    expect($response)->toBeInstanceOf(AgentResponse::class);
    expect($response->text)->toBe('Bonjour ! Je suis l\'assistant.');
});

test('tool exception does not crash the request', function () {
    Ai::fakeAgent(ConversationalAgent::class, ['Voici une comparaison.']);

    $agent = ConversationalAgent::make()
        ->continue('test-conv-2', $this->user);

    $response = $agent->prompt('Compare les candidats');

    expect($response)->toBeInstanceOf(AgentResponse::class);
    expect($response->text)->not->toBeEmpty();
});

test('empty tool result returns not found message', function () {
    Ai::fakeAgent(ConversationalAgent::class, [
        'Non trouvé.',   // main response
        'Titre',          // title generation
    ]);
    actingAs($this->user);

    $agent = ConversationalAgent::make()
        ->forUser($this->user);

    $response = $agent->prompt('Que vaut le candidat ID 999 ?');

    expect($response)->toBeInstanceOf(AgentResponse::class);
    expect($response->text)->toBe('Non trouvé.');
});

test('new conversation is auto-created when no ID is set', function () {
    Ai::fakeAgent(ConversationalAgent::class, [
        'Nouvelle conversation créée.',  // main response
        'Titre auto',                     // title generation
    ]);

    $agent = ConversationalAgent::make()
        ->forUser($this->user);

    expect($agent->currentConversation())->toBeNull();

    $response = $agent->prompt('Présente-toi');

    expect($response)->toBeInstanceOf(AgentResponse::class);
    expect($response->conversationId)->not->toBeNull();
    expect($response->text)->toBe('Nouvelle conversation créée.');
});

test('existing conversation is continued via continue()', function () {
    Ai::fakeAgent(ConversationalAgent::class, ['Premier message.']);
    $conversationId = 'test-continue-conv';

    $agent = ConversationalAgent::make()
        ->continue($conversationId, $this->user);

    expect($agent->currentConversation())->toBe($conversationId);

    $response = $agent->prompt('Premier message');

    expect($response->conversationId)->toBe($conversationId);
    expect($response->text)->toBe('Premier message.');
});
