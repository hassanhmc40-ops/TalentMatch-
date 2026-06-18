<?php

use App\Jobs\AnalyseCvJob;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    Queue::fake();
});

test('submit candidate creates pending analysis and dispatches job', function () {
    actingAs($this->user);

    $response = $this->post(route('offres.candidats.submit', $this->jobOffer), [
        'nom' => 'Jean Dupont',
        'cv_text' => 'Expérience en PHP et Laravel depuis 5 ans.',
    ]);

    $response->assertRedirect(route('offres.show', $this->jobOffer));
    $response->assertSessionHas('success', "Candidature soumise. L'analyse est en cours.");

    $candidate = Candidate::first();
    expect($candidate)->not->toBeNull();
    expect($candidate->name)->toBe('Jean Dupont');

    $analysis = CandidateAnalysis::first();
    expect($analysis)->not->toBeNull();
    expect($analysis->status)->toBe('pending');
    expect($analysis->job_offer_id)->toBe($this->jobOffer->id);
    expect($analysis->candidate_id)->toBe($candidate->id);

    Queue::assertPushed(AnalyseCvJob::class, function ($job) use ($candidate) {
        return $job->candidateId === $candidate->id
            && $job->jobOfferId === $this->jobOffer->id;
    });
});

test('submit candidate rejects duplicate candidate for same offer', function () {
    actingAs($this->user);

    $candidate = Candidate::factory()->create(['name' => 'Jean Dupont']);
    CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->jobOffer->id,
        'candidate_id' => $candidate->id,
    ]);

    $response = $this->post(route('offres.candidats.submit', $this->jobOffer), [
        'nom' => '  Jean Dupont  ',
        'cv_text' => 'Expérience en PHP.',
    ]);

    $response->assertRedirect(route('offres.show', $this->jobOffer));
    $response->assertSessionHasErrors(['nom' => 'Ce candidat a déjà été soumis pour cette offre.']);
});

test('submit candidate rejects empty name with French error', function () {
    actingAs($this->user);

    $response = $this->post(route('offres.candidats.submit', $this->jobOffer), [
        'nom' => '',
        'cv_text' => 'Expérience en PHP.',
    ]);

    $response->assertSessionHasErrors(['nom' => 'Le nom du candidat est obligatoire.']);
});

test('submit candidate rejects empty CV text with French error', function () {
    actingAs($this->user);

    $response = $this->post(route('offres.candidats.submit', $this->jobOffer), [
        'nom' => 'Jean Dupont',
        'cv_text' => '',
    ]);

    $response->assertSessionHasErrors(['cv_text' => 'Le texte du CV est obligatoire.']);
});

test('guest cannot submit candidate', function () {
    $response = $this->post(route('offres.candidats.submit', $this->jobOffer), [
        'nom' => 'Jean Dupont',
        'cv_text' => 'Expérience en PHP.',
    ]);

    $response->assertRedirect(route('login'));
});

test('user cannot submit candidate for another users offer', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);

    actingAs($otherUser);

    $response = $this->post(route('offres.candidats.submit', $this->jobOffer), [
        'nom' => 'Jean Dupont',
        'cv_text' => 'Expérience en PHP.',
    ]);

    $response->assertForbidden();
});
