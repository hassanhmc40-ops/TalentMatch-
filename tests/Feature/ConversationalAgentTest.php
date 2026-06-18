<?php

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;
use Laravel\Ai\Tools\Request;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($this->user);
});

test('GetCandidateAnalysis returns data for authorized user', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate = Candidate::factory()->create();
    $analysis = CandidateAnalysis::factory()->create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate->id,
        'matching_score' => 75,
    ]);

    $tool = new GetCandidateAnalysis;
    $result = $tool->handle(new Request(['candidat_id' => $candidate->id]));

    expect($result)->toContain('75');
    expect($result)->toContain($candidate->name);
});

test('GetCandidateAnalysis returns error for unauthorized user', function () {
    $otherUser = User::factory()->create();
    $jobOffer = JobOffer::factory()->create(['user_id' => $otherUser->id]);
    $candidate = Candidate::factory()->create();
    CandidateAnalysis::factory()->create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate->id,
    ]);

    $tool = new GetCandidateAnalysis;
    $result = $tool->handle(new Request(['candidat_id' => $candidate->id]));

    expect($result)->toContain('Analyse non trouvée ou accès non autorisé.');
});

test('GetJobRequirements returns data for authorized user', function () {
    $jobOffer = JobOffer::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Développeur PHP',
    ]);

    $tool = new GetJobRequirements;
    $result = $tool->handle(new Request(['offre_id' => $jobOffer->id]));

    expect($result)->toContain('Développeur PHP');
});

test('GetJobRequirements returns error for unauthorized user', function () {
    $otherUser = User::factory()->create();
    $jobOffer = JobOffer::factory()->create([
        'user_id' => $otherUser->id,
        'title' => 'Développeur PHP',
    ]);

    $tool = new GetJobRequirements;
    $result = $tool->handle(new Request(['offre_id' => $jobOffer->id]));

    expect($result)->toContain('Offre non trouvée ou accès non autorisé.');
});

test('CompareCandidates returns comparison for authorized user', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate1 = Candidate::factory()->create(['name' => 'Alice']);
    $candidate2 = Candidate::factory()->create(['name' => 'Bob']);
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate1->id,
        'matching_score' => 80,
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate2->id,
        'matching_score' => 60,
    ]);

    $tool = new CompareCandidates;
    $result = $tool->handle(new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]));

    expect($result)->toContain('Alice');
    expect($result)->toContain('Bob');
    expect($result)->toContain('20');
});

test('CompareCandidates returns error for cross-offer comparison', function () {
    $offer1 = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $offer2 = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate = Candidate::factory()->create();
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer1->id,
        'candidate_id' => $candidate->id,
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer2->id,
        'candidate_id' => $candidate->id,
    ]);

    $tool = new CompareCandidates;
    $result = $tool->handle(new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]));

    expect($result)->toContain('Impossible de comparer');
});

test('CompareCandidates returns error when user does not own one analysis', function () {
    $otherUser = User::factory()->create();
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $otherOffer = JobOffer::factory()->create(['user_id' => $otherUser->id]);
    $candidate = Candidate::factory()->create();
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate->id,
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $otherOffer->id,
        'candidate_id' => $candidate->id,
    ]);

    $tool = new CompareCandidates;
    $result = $tool->handle(new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]));

    expect($result)->toContain('Analyse non trouvée ou accès non autorisé.');
});
