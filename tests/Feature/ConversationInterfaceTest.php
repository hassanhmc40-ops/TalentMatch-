<?php

use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($this->user);

    $this->offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $this->candidate = Candidate::factory()->create();
    $this->analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'candidate_id' => $this->candidate->id,
    ]);
});

test('conversation page shows welcome state when no messages exist', function () {
    $this->get(route('conversations.show', [$this->offer, $this->candidate]))
        ->assertOk()
        ->assertSee('Assistant RH')
        ->assertSee($this->candidate->name)
        ->assertSee($this->offer->title);
});

test('conversation page loads messages from database', function () {
    $conversationId = 'candidate-analysis-'.$this->analysis->id;

    AgentConversation::create([
        'id' => $conversationId,
        'user_id' => $this->user->id,
        'title' => 'Test',
    ]);

    $defaults = [
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ];

    AgentConversationMessage::create(array_merge($defaults, [
        'id' => 'msg-1',
        'conversation_id' => $conversationId,
        'user_id' => $this->user->id,
        'agent' => 'user',
        'role' => 'user',
        'content' => 'Quel est le score ?',
    ]));

    AgentConversationMessage::create(array_merge($defaults, [
        'id' => 'msg-2',
        'conversation_id' => $conversationId,
        'user_id' => null,
        'agent' => 'assistant',
        'role' => 'assistant',
        'content' => 'Le score est de 75%.',
    ]));

    $this->get(route('conversations.show', [$this->offer, $this->candidate]))
        ->assertOk()
        ->assertSee('Quel est le score ?')
        ->assertSee('Le score est de 75%.');
});

test('conversation page does not load other conversations messages', function () {
    $otherUser = User::factory()->create();
    $otherOffer = JobOffer::factory()->create(['user_id' => $otherUser->id]);
    $otherCandidate = Candidate::factory()->create();
    $otherAnalysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $otherOffer->id,
        'candidate_id' => $otherCandidate->id,
    ]);

    $otherConvId = 'candidate-analysis-'.$otherAnalysis->id;
    AgentConversation::create([
        'id' => $otherConvId,
        'user_id' => $otherUser->id,
        'title' => 'Other',
    ]);
    $defaults = [
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ];

    AgentConversationMessage::create(array_merge($defaults, [
        'id' => 'other-msg',
        'conversation_id' => $otherConvId,
        'user_id' => $otherUser->id,
        'agent' => 'user',
        'role' => 'user',
        'content' => 'Message from other conversation',
    ]));

    $this->get(route('conversations.show', [$this->offer, $this->candidate]))
        ->assertOk()
        ->assertDontSee('Message from other conversation');
});

test('stream endpoint requires valid message', function () {
    $this->get(route('conversations.stream', [$this->offer, $this->candidate]))
        ->assertSessionHasErrors('message');
});

test('stream endpoint returns 403 for unauthorized user', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($otherUser);

    $this->get(route('conversations.stream', [$this->offer, $this->candidate]).'?message=Test')
        ->assertForbidden();
});
