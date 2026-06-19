<?php

use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays conversations section on dashboard for user with conversations', function () {
    $offer = JobOffer::factory()->for($this->user)->create();
    $candidate = Candidate::factory()->create();
    $analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'candidate_id' => $candidate->id,
    ]);

    AgentConversation::create([
        'id' => (string) str()->uuid(),
        'user_id' => $this->user->id,
        'title' => 'Analyse de Jane',
        'candidate_analysis_id' => $analysis->id,
    ]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Conversations récentes')
        ->assertSee($candidate->name)
        ->assertSee($offer->title);
});

it('shows empty state on dashboard when user has no conversations', function () {
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Conversations récentes');
});

it('excludes conversations without candidate_analysis_id', function () {
    AgentConversation::create([
        'id' => (string) str()->uuid(),
        'user_id' => $this->user->id,
        'title' => 'Orpheline',
        'candidate_analysis_id' => null,
    ]);

    AgentConversation::create([
        'id' => (string) str()->uuid(),
        'user_id' => $this->user->id,
        'title' => 'Liée',
        'candidate_analysis_id' => CandidateAnalysis::factory()->create()->id,
    ]);

    $conversations = AgentConversation::forDashboard($this->user->id)->get();

    expect($conversations->count())->toBe(1);
    expect($conversations->first()->title)->toBe('Liée');
});

it('scopes conversations to authenticated user only', function () {
    $otherUser = User::factory()->create();
    $offer = JobOffer::factory()->for($this->user)->create();
    $candidate = Candidate::factory()->create();
    $analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'candidate_id' => $candidate->id,
    ]);

    AgentConversation::create([
        'id' => 'my-conv',
        'user_id' => $this->user->id,
        'title' => 'Ma conversation',
        'candidate_analysis_id' => $analysis->id,
    ]);

    $otherOffer = JobOffer::factory()->for($otherUser)->create();
    $otherCandidate = Candidate::factory()->create();
    $otherAnalysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $otherOffer->id,
        'candidate_id' => $otherCandidate->id,
    ]);

    AgentConversation::create([
        'id' => 'other-conv',
        'user_id' => $otherUser->id,
        'title' => 'Autre conversation',
        'candidate_analysis_id' => $otherAnalysis->id,
    ]);

    $conversations = AgentConversation::forDashboard($this->user->id)->get();

    expect($conversations->count())->toBe(1);
    expect($conversations->first()->title)->toBe('Ma conversation');
});

it('includes message count in conversation data', function () {
    $offer = JobOffer::factory()->for($this->user)->create();
    $candidate = Candidate::factory()->create();
    $analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'candidate_id' => $candidate->id,
    ]);

    $conv = AgentConversation::create([
        'id' => 'msg-count-conv',
        'user_id' => $this->user->id,
        'title' => 'Test',
        'candidate_analysis_id' => $analysis->id,
    ]);

    AgentConversationMessage::create([
        'id' => (string) str()->uuid(),
        'conversation_id' => $conv->id,
        'user_id' => $this->user->id,
        'agent' => 'test',
        'role' => 'user',
        'content' => 'Bonjour',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    AgentConversationMessage::create([
        'id' => (string) str()->uuid(),
        'conversation_id' => $conv->id,
        'user_id' => $this->user->id,
        'agent' => 'test',
        'role' => 'assistant',
        'content' => 'Bonjour',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    $conversations = AgentConversation::forDashboard($this->user->id)->get();

    expect($conversations)->toHaveCount(1);
    expect($conversations->first()->messages_count)->toBe(2);
});

it('renders candidate name and offer name in conversation rows', function () {
    $offer = JobOffer::factory()->for($this->user)->create(['title' => 'Développeur PHP']);
    $candidate = Candidate::factory()->create(['name' => 'Jane Doe']);
    $analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'candidate_id' => $candidate->id,
    ]);

    AgentConversation::create([
        'id' => (string) str()->uuid(),
        'user_id' => $this->user->id,
        'title' => 'Analyse de Jane',
        'candidate_analysis_id' => $analysis->id,
    ]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Jane Doe')
        ->assertSee('Développeur PHP');
});
