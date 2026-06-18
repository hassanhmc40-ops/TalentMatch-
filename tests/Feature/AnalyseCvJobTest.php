<?php

use App\Actions\PersistValidatedAnalysis;
use App\Actions\ValidateStructuredAnalysis;
use App\Ai\Agents\CvAnalysisAgent;
use App\Exceptions\ValidationFailedException;
use App\Jobs\AnalyseCvJob;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);

    Queue::fake();
});

test('AnalyseCvJob dispatches and creates completed analysis for valid AI response', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate = Candidate::factory()->create();

    $validResponse = [
        'competences_extraites' => ['PHP', 'Laravel'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français', 'Anglais'],
        'matching_score' => 78,
        'points_forts' => ['Expérience en gestion de projet'],
        'lacunes' => ['Manque de certification'],
        'competences_manquantes' => ['Docker'],
        'recommandation' => 'convoquer',
        'justification' => 'Profil correspond bien aux exigences du poste.',
    ];

    CvAnalysisAgent::fake([$validResponse]);

    $job = new AnalyseCvJob($candidate->id, $jobOffer->id);
    $job->handle(
        new ValidateStructuredAnalysis,
        new PersistValidatedAnalysis(new ValidateStructuredAnalysis),
    );

    $analysis = CandidateAnalysis::query()
        ->where('candidate_id', $candidate->id)
        ->where('job_offer_id', $jobOffer->id)
        ->first();

    expect($analysis)->not->toBeNull();
    expect($analysis->status)->toBe('completed');
    expect($analysis->matching_score)->toBe(78);
});

test('AnalyseCvJob sets failed status on validation failure', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate = Candidate::factory()->create();

    CandidateAnalysis::create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate->id,
        'status' => 'pending',
        'extracted_skills' => [],
        'years_experience' => 0,
        'education_level' => '',
        'languages' => [],
        'matching_score' => 0,
        'strengths' => [],
        'gaps' => [],
        'missing_skills' => [],
        'recommendation' => 'attente',
        'justification' => '',
    ]);

    $invalidResponse = [
        'competences_extraites' => ['PHP'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français'],
        'matching_score' => 150,
        'points_forts' => ['Test'],
        'lacunes' => [],
        'competences_manquantes' => [],
        'recommandation' => 'attente',
        'justification' => 'Test.',
    ];

    CvAnalysisAgent::fake([$invalidResponse]);

    $job = new AnalyseCvJob($candidate->id, $jobOffer->id);

    try {
        $job->handle(
            new ValidateStructuredAnalysis,
            new PersistValidatedAnalysis(new ValidateStructuredAnalysis),
        );
    } catch (ValidationFailedException) {
    }

    $analysis = CandidateAnalysis::query()
        ->where('candidate_id', $candidate->id)
        ->where('job_offer_id', $jobOffer->id)
        ->first();

    expect($analysis->status)->toBe('failed');
});

test('AnalyseCvJob calls AI agent with prompt containing candidate CV and offer details', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate = Candidate::factory()->create([
        'cv_text' => 'Expert PHP depuis 5 ans.',
    ]);

    $validResponse = [
        'competences_extraites' => ['PHP'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français'],
        'matching_score' => 50,
        'points_forts' => ['Expérience'],
        'lacunes' => [],
        'competences_manquantes' => [],
        'recommandation' => 'attente',
        'justification' => 'Test.',
    ];

    CvAnalysisAgent::fake([$validResponse]);

    $job = new AnalyseCvJob($candidate->id, $jobOffer->id);
    $job->handle(
        new ValidateStructuredAnalysis,
        new PersistValidatedAnalysis(new ValidateStructuredAnalysis),
    );

    CvAnalysisAgent::assertPrompted(function (\Laravel\Ai\Prompts\AgentPrompt $prompt): bool {
        return str_contains($prompt->prompt, 'Expert PHP depuis 5 ans.')
            && str_contains($prompt->prompt, 'Analysez le CV suivant');
    });
});

test('AnalyseCvJob has declarative retry properties', function () {
    $candidate = Candidate::factory()->create();
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    $job = new AnalyseCvJob($candidate->id, $jobOffer->id);

    expect($job->tries)->toBe(3);
    expect($job->backoff)->toBe([30]);
});

test('AnalyseCvJob failed method sets analysis status to failed', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $candidate = Candidate::factory()->create();

    CandidateAnalysis::create([
        'job_offer_id' => $jobOffer->id,
        'candidate_id' => $candidate->id,
        'status' => 'pending',
        'extracted_skills' => [],
        'years_experience' => 0,
        'education_level' => '',
        'languages' => [],
        'matching_score' => 0,
        'strengths' => [],
        'gaps' => [],
        'missing_skills' => [],
        'recommendation' => 'attente',
        'justification' => '',
    ]);

    $job = new AnalyseCvJob($candidate->id, $jobOffer->id);
    $job->failed(new Exception('AI service unavailable'));

    $analysis = CandidateAnalysis::query()
        ->where('candidate_id', $candidate->id)
        ->where('job_offer_id', $jobOffer->id)
        ->first();

    expect($analysis->status)->toBe('failed');
});
