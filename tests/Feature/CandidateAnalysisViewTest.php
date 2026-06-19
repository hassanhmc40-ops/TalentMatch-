<?php

use App\Enums\Recommendation;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->otherUser = User::factory()->create(['email_verified_at' => now()]);
    $this->jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $this->candidate = Candidate::factory()->create();
    $this->analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->jobOffer->id,
        'candidate_id' => $this->candidate->id,
        'status' => 'completed',
    ]);
});

test('analysis detail page returns 200 for authorized user', function () {
    $this->actingAs($this->user)
        ->get(route('analyses.show', [$this->jobOffer, $this->analysis]))
        ->assertOk();
});

test('analysis detail page returns 403 for unauthorized user', function () {
    $this->actingAs($this->otherUser)
        ->get(route('analyses.show', [$this->jobOffer, $this->analysis]))
        ->assertForbidden();
});

test('analysis detail page returns 404 for non-existent analysis', function () {
    $this->actingAs($this->user)
        ->get(route('analyses.show', [$this->jobOffer, 99999]))
        ->assertNotFound();
});

test('pending analysis shows en cours message', function () {
    $this->analysis->update(['status' => 'pending']);

    $this->actingAs($this->user)
        ->get(route('analyses.show', [$this->jobOffer, $this->analysis]))
        ->assertOk()
        ->assertSee('Analyse en cours...');
});

test('failed analysis shows echeouee message', function () {
    $this->analysis->update(['status' => 'failed']);

    $this->actingAs($this->user)
        ->get(route('analyses.show', [$this->jobOffer, $this->analysis]))
        ->assertOk()
        ->assertSee('Analyse échouée');
});

test('completed analysis displays score progress bar', function () {
    $this->analysis->update(['matching_score' => 75, 'status' => 'completed']);

    $this->actingAs($this->user)
        ->get(route('analyses.show', [$this->jobOffer, $this->analysis]))
        ->assertOk()
        ->assertSee('75%')
        ->assertSee('Bon');
});

test('completed analysis displays recommendation badge', function () {
    $this->analysis->update(['recommendation' => Recommendation::Convoquer, 'status' => 'completed']);

    $this->actingAs($this->user)
        ->get(route('analyses.show', [$this->jobOffer, $this->analysis]))
        ->assertOk()
        ->assertSee('À convoquer');
});

test('offer detail page includes link to analysis detail', function () {
    $this->actingAs($this->user)
        ->get(route('offres.show', $this->jobOffer))
        ->assertOk()
        ->assertSee('Voir l\'analyse')
        ->assertSee('Assistant →');
});
