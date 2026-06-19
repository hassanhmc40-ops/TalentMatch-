<?php

use App\Enums\Recommendation;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

test('comparison page shows both candidates with scores and strengths', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $analysisA = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 85,
        'recommendation' => Recommendation::Convoquer,
    ]);
    $analysisB = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 60,
        'recommendation' => Recommendation::Attente,
    ]);

    actingAs($this->user)
        ->get(route('offres.comparer', [
            $offer,
            'candidats' => [$analysisA->candidate_id, $analysisB->candidate_id],
        ]))
        ->assertOk()
        ->assertSeeText($analysisA->candidate->name)
        ->assertSeeText($analysisB->candidate->name)
        ->assertSeeText('85%')
        ->assertSeeText('60%')
        ->assertSeeText('Écart de score')
        ->assertSeeText('25')
        ->assertSeeText('À convoquer');
});

test('comparison page returns 403 for unauthorized offer', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $offer = JobOffer::factory()->create(['user_id' => $otherUser->id]);
    $analysisA = CandidateAnalysis::factory()->create(['job_offer_id' => $offer->id]);
    $analysisB = CandidateAnalysis::factory()->create(['job_offer_id' => $offer->id]);

    actingAs($this->user)
        ->get(route('offres.comparer', [
            $offer,
            'candidats' => [$analysisA->candidate_id, $analysisB->candidate_id],
        ]))
        ->assertForbidden();
});

test('comparison page returns 404 for non-existent offer', function () {
    actingAs($this->user)
        ->get(route('offres.comparer', [99999, 'candidats' => [1, 2]]))
        ->assertNotFound();
});

test('redirect with error when less than two candidates provided', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.comparer', [$offer, 'candidats' => [1]]))
        ->assertRedirect(route('offres.show', $offer))
        ->assertSessionHasErrors(['comparer']);
});

test('redirect with error when candidates not in offer', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.comparer', [$offer, 'candidats' => [999, 888]]))
        ->assertRedirect(route('offres.show', $offer))
        ->assertSessionHasErrors(['comparer']);
});

test('pending analysis shows appropriate status message on comparison page', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $analysisA = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'status' => 'pending',
    ]);
    $analysisB = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'status' => 'completed',
    ]);

    actingAs($this->user)
        ->get(route('offres.comparer', [
            $offer,
            'candidats' => [$analysisA->candidate_id, $analysisB->candidate_id],
        ]))
        ->assertOk()
        ->assertSeeText('Analyse en cours...');
});

test('failed analysis shows appropriate status message on comparison page', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $analysisA = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'status' => 'failed',
    ]);
    $analysisB = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'status' => 'completed',
    ]);

    actingAs($this->user)
        ->get(route('offres.comparer', [
            $offer,
            'candidats' => [$analysisA->candidate_id, $analysisB->candidate_id],
        ]))
        ->assertOk()
        ->assertSeeText('Analyse échouée');
});
