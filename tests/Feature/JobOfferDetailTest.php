<?php

use App\Enums\Recommendation;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

test('authenticated owner sees offer criteria', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertSeeText($offer->title)
        ->assertSeeText($offer->description);
});

test('unauthenticated user is redirected to login', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    get(route('offres.show', $offer))
        ->assertRedirect(route('login'));
});

test('non-owner gets 403', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $offer = JobOffer::factory()->create(['user_id' => $otherUser->id]);

    actingAs($this->user)
        ->get(route('offres.show', $offer))
        ->assertForbidden();
});

test('non-existent offer returns 404', function () {
    actingAs($this->user)
        ->get(route('offres.show', 99999))
        ->assertNotFound();
});

test('analyzed candidates table is displayed with scores and recommendations', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 85,
        'recommendation' => Recommendation::Convoquer,
    ]);

    actingAs($this->user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertSeeText($analysis->candidate->name)
        ->assertSeeText('85%')
        ->assertSeeText('À convoquer');
});

test('empty state when no candidates analyzed', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertSeeText('Aucun candidat analysé pour cette offre.');
});

test('recommendation enum label returns French text', function () {
    expect(Recommendation::Convoquer->label())->toBe('À convoquer');
    expect(Recommendation::Attente->label())->toBe('En attente');
    expect(Recommendation::Rejeter->label())->toBe('À rejeter');
});

test('CandidateAnalysis model stores and casts data correctly', function () {
    $analysis = CandidateAnalysis::factory()->create();

    expect($analysis->extracted_skills)->toBeArray();
    expect($analysis->matching_score)->toBeInt();
    expect($analysis->matching_score)->toBeBetween(0, 100);
    expect($analysis->recommendation)->toBeInstanceOf(Recommendation::class);
    expect($analysis->jobOffer)->toBeInstanceOf(JobOffer::class);
});
