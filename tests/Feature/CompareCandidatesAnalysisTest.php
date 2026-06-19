<?php

use App\Ai\Tools\CompareCandidates;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;
use Laravel\Ai\Tools\Request;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    actingAs($this->user);

    $this->tool = new CompareCandidates;

    $this->offer = JobOffer::factory()->create([
        'user_id' => $this->user->id,
        'min_experience_years' => 3,
    ]);
});

test('comparison score is computed correctly with weighted dimensions', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 85,
        'years_experience' => 3,
        'education_level' => 'Bac+5',
        'extracted_skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript'],
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 60,
        'years_experience' => 1,
        'education_level' => 'Bac+2',
        'extracted_skills' => ['PHP', 'JavaScript'],
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response)->toHaveKey('comparaison_score');
    expect($response['comparaison_score'])->toBeInt();
    expect($response['comparaison_score'])->toBeBetween(0, 100);
    expect($response['comparaison_score'])->toBeGreaterThan(50);
});

test('clear verdict when comparison score >= 60', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 90,
        'years_experience' => 3,
        'education_level' => 'Bac+5',
        'extracted_skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Docker'],
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 40,
        'years_experience' => 1,
        'education_level' => 'Bac',
        'extracted_skills' => ['PHP'],
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response['comparaison_score'])->toBeGreaterThanOrEqual(60);
    expect($response['verdict'])->toContain('recommandé');
    expect($response['candidat_recommande'])->toBe('candidat_1');
});

test('nuanced verdict when comparison score between 41 and 59', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 35,
        'years_experience' => 1,
        'education_level' => 'Bac',
        'extracted_skills' => ['PHP'],
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 30,
        'years_experience' => 0,
        'education_level' => 'Bac',
        'extracted_skills' => ['JavaScript'],
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response['comparaison_score'])->toBeBetween(41, 59);
    expect($response['verdict'])->toContain('légèrement');
    expect($response['candidat_recommande'])->not->toBeNull();
});

test('toss-up verdict when comparison score <= 40', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 30,
        'years_experience' => 0,
        'education_level' => '',
        'extracted_skills' => ['PHP'],
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 30,
        'years_experience' => 0,
        'education_level' => '',
        'extracted_skills' => ['PHP'],
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response['comparaison_score'])->toBeLessThanOrEqual(40);
    expect($response['verdict'])->toContain('très proches');
    expect($response['candidat_recommande'])->toBeNull();
});

test('skill gap analysis returns correct exclusive skills', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 75,
        'extracted_skills' => ['PHP', 'Laravel', 'MySQL', 'Docker'],
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 70,
        'extracted_skills' => ['PHP', 'JavaScript', 'React'],
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response['competences_exclusives_candidat_1'])->toContain('laravel', 'mysql', 'docker');
    expect($response['competences_exclusives_candidat_1'])->not->toContain('php');
    expect($response['competences_exclusives_candidat_2'])->toContain('javascript', 'react');
    expect($response['competences_exclusives_candidat_2'])->not->toContain('php');
});

test('empty exclusive skills when both candidates have identical skills', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 70,
        'extracted_skills' => ['PHP', 'Laravel', 'MySQL'],
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
        'matching_score' => 70,
        'extracted_skills' => ['PHP', 'Laravel', 'MySQL'],
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response['competences_exclusives_candidat_1'])->toBeEmpty();
    expect($response['competences_exclusives_candidat_2'])->toBeEmpty();
});

test('backward compatibility  existing fields are still present', function () {
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->offer->id,
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = json_decode($this->tool->handle($request), true);

    expect($response)->toHaveKey('candidat_1');
    expect($response)->toHaveKey('candidat_2');
    expect($response)->toHaveKey('difference_score');
    expect($response['candidat_1'])->toHaveKey('nom');
    expect($response['candidat_1'])->toHaveKey('score');
    expect($response['candidat_1'])->toHaveKey('points_forts');
    expect($response['candidat_1'])->toHaveKey('lacunes');
    expect($response['candidat_1'])->toHaveKey('recommandation');
});

test('cross-offer comparison returns error', function () {
    $otherOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $analysis1 = CandidateAnalysis::factory()->create(['job_offer_id' => $this->offer->id]);
    $analysis2 = CandidateAnalysis::factory()->create(['job_offer_id' => $otherOffer->id]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = $this->tool->handle($request);

    expect($response)->toBe('Erreur : Impossible de comparer des candidats de différentes offres.');
});

test('unauthorized access returns error', function () {
    $otherUser = User::factory()->create();
    $otherOffer = JobOffer::factory()->create([
        'user_id' => $otherUser->id,
        'min_experience_years' => 3,
    ]);
    $analysis1 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $otherOffer->id,
        'matching_score' => 85,
    ]);
    $analysis2 = CandidateAnalysis::factory()->create([
        'job_offer_id' => $otherOffer->id,
        'matching_score' => 75,
    ]);

    $request = new Request([
        'analyse_id_1' => $analysis1->id,
        'analyse_id_2' => $analysis2->id,
    ]);

    $response = $this->tool->handle($request);

    expect($response)->toBe('Analyse non trouvée ou accès non autorisé.');
});
