<?php

use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $this->candidate = Candidate::factory()->create();
    $this->analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->jobOffer->id,
        'candidate_id' => $this->candidate->id,
    ]);
});

test('migration adds candidate_analysis_id column to agent_conversations table', function () {
    $tableName = Config::get('ai.conversations.tables.conversations', 'agent_conversations');

    expect(Schema::hasColumn($tableName, 'candidate_analysis_id'))->toBeTrue();

    $column = Schema::getColumnType($tableName, 'candidate_analysis_id');
    expect($column)->toBe('integer');
});

test('migration adds composite index on candidate_analysis_id user_id updated_at', function () {
    $tableName = Config::get('ai.conversations.tables.conversations', 'agent_conversations');

    $indexes = collect(Schema::getIndexes($tableName));
    $index = $indexes->firstWhere('name', 'conv_analysis_user_idx');

    expect($index)->not->toBeNull();
    expect($index['columns'])->toContain('candidate_analysis_id');
    expect($index['columns'])->toContain('user_id');
    expect($index['columns'])->toContain('updated_at');
});

test('candidate_analysis_id column is nullable', function () {
    $tableName = Config::get('ai.conversations.tables.conversations', 'agent_conversations');

    $column = collect(Schema::getColumns($tableName))
        ->firstWhere('name', 'candidate_analysis_id');

    expect($column['nullable'])->toBeTrue();
});

test('AgentConversation model uses config-based table name', function () {
    $model = new AgentConversation;

    expect($model->getTable())->toBe(
        Config::get('ai.conversations.tables.conversations', 'agent_conversations')
    );
});

test('AgentConversation model uses string primary key', function () {
    $model = new AgentConversation;

    expect($model->getIncrementing())->toBeFalse();
    expect($model->getKeyType())->toBe('string');
});

test('AgentConversation has candidateAnalysis relationship', function () {
    $conversation = AgentConversation::create([
        'id' => 'test-conversation-ca',
        'user_id' => $this->user->id,
        'title' => 'Test',
        'candidate_analysis_id' => $this->analysis->id,
    ]);

    expect($conversation->candidateAnalysis)->toBeInstanceOf(CandidateAnalysis::class);
    expect($conversation->candidateAnalysis->id)->toBe($this->analysis->id);
});

test('AgentConversation has user relationship', function () {
    $conversation = AgentConversation::create([
        'id' => 'test-conversation-user',
        'user_id' => $this->user->id,
        'title' => 'Test',
        'candidate_analysis_id' => $this->analysis->id,
    ]);

    expect($conversation->user)->toBeInstanceOf(User::class);
    expect($conversation->user->id)->toBe($this->user->id);
});

test('AgentConversation has messages relationship ordered by created_at', function () {
    $conversation = AgentConversation::create([
        'id' => 'test-conversation-msgs',
        'user_id' => $this->user->id,
        'title' => 'Test',
        'candidate_analysis_id' => $this->analysis->id,
    ]);

    AgentConversationMessage::create([
        'id' => (string) str()->uuid(),
        'conversation_id' => $conversation->id,
        'user_id' => $this->user->id,
        'agent' => 'test-agent',
        'role' => 'user',
        'content' => 'Hello',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    AgentConversationMessage::create([
        'id' => (string) str()->uuid(),
        'conversation_id' => $conversation->id,
        'user_id' => $this->user->id,
        'agent' => 'test-agent',
        'role' => 'assistant',
        'content' => 'Bonjour',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    $messages = $conversation->messages;
    expect($messages)->toHaveCount(2);
    expect($messages[0]->role)->toBe('user');
    expect($messages[1]->role)->toBe('assistant');
});

test('AgentConversationMessage model uses config-based table name', function () {
    $model = new AgentConversationMessage;

    expect($model->getTable())->toBe(
        Config::get('ai.conversations.tables.messages', 'agent_conversation_messages')
    );
});

test('AgentConversationMessage model uses string primary key', function () {
    $model = new AgentConversationMessage;

    expect($model->getIncrementing())->toBeFalse();
    expect($model->getKeyType())->toBe('string');
});

test('AgentConversationMessage has conversation relationship', function () {
    $conversation = AgentConversation::create([
        'id' => 'test-conversation-msg-rel',
        'user_id' => $this->user->id,
        'title' => 'Test',
        'candidate_analysis_id' => $this->analysis->id,
    ]);

    $message = AgentConversationMessage::create([
        'id' => (string) str()->uuid(),
        'conversation_id' => $conversation->id,
        'user_id' => $this->user->id,
        'agent' => 'test-agent',
        'role' => 'user',
        'content' => 'Hello',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    expect($message->conversation)->toBeInstanceOf(AgentConversation::class);
    expect($message->conversation->id)->toBe($conversation->id);
});

test('CandidateAnalysis has conversations relationship', function () {
    AgentConversation::create([
        'id' => 'test-conversation-ca-rel',
        'user_id' => $this->user->id,
        'title' => 'Test',
        'candidate_analysis_id' => $this->analysis->id,
    ]);

    $conversations = $this->analysis->fresh()->conversations;
    expect($conversations)->toHaveCount(1);
    expect($conversations->first()->candidate_analysis_id)->toBe($this->analysis->id);
});

test('AgentConversation byUser scope filters by user', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);

    AgentConversation::create([
        'id' => 'own-conversation',
        'user_id' => $this->user->id,
        'title' => 'Ma conversation',
        'candidate_analysis_id' => $this->analysis->id,
    ]);

    $otherJobOffer = JobOffer::factory()->create(['user_id' => $otherUser->id]);
    $otherCandidate = Candidate::factory()->create();
    $otherAnalysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $otherJobOffer->id,
        'candidate_id' => $otherCandidate->id,
    ]);

    AgentConversation::create([
        'id' => 'other-conversation',
        'user_id' => $otherUser->id,
        'title' => 'Autre conversation',
        'candidate_analysis_id' => $otherAnalysis->id,
    ]);

    $ownConversations = AgentConversation::byUser($this->user->id)->get();
    expect($ownConversations)->toHaveCount(1);
    expect($ownConversations->first()->id)->toBe('own-conversation');
});

test('candidate_analysis_id is stored after conversation creation', function () {
    $conversationId = 'candidate-analysis-'.$this->analysis->id;

    AgentConversation::create([
        'id' => $conversationId,
        'user_id' => $this->user->id,
        'title' => 'Test conversation',
    ]);

    AgentConversation::where('id', $conversationId)
        ->whereNull('candidate_analysis_id')
        ->update(['candidate_analysis_id' => $this->analysis->id]);

    $this->assertDatabaseHas('agent_conversations', [
        'id' => $conversationId,
        'candidate_analysis_id' => $this->analysis->id,
    ]);
});

test('follow-up messages preserve the same candidate_analysis_id', function () {
    $conversationId = 'candidate-analysis-'.$this->analysis->id;

    AgentConversation::create([
        'id' => $conversationId,
        'user_id' => $this->user->id,
        'title' => 'Test conversation',
    ]);

    AgentConversation::where('id', $conversationId)
        ->whereNull('candidate_analysis_id')
        ->update(['candidate_analysis_id' => $this->analysis->id]);

    AgentConversation::where('id', $conversationId)
        ->whereNull('candidate_analysis_id')
        ->update(['candidate_analysis_id' => $this->analysis->id]);

    $conversation = AgentConversation::find($conversationId);
    expect($conversation->candidate_analysis_id)->toBe($this->analysis->id);
});
