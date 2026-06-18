<?php

use App\Actions\PersistValidatedAnalysis;
use App\Actions\ValidateStructuredAnalysis;
use App\Enums\Recommendation;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $this->candidate = Candidate::factory()->create();
    $this->persister = new PersistValidatedAnalysis(new ValidateStructuredAnalysis);
});

test('persist creates analysis record with completed status', function () {
    $data = [
        'competences_extraites' => ['PHP', 'Laravel'],
        'annees_experience' => 5,
        'niveau_etudes' => 'Bac+5',
        'langues' => ['Français', 'Anglais'],
        'matching_score' => 78,
        'points_forts' => ['Expérience en gestion'],
        'lacunes' => ['Manque de certification'],
        'competences_manquantes' => ['Docker'],
        'recommandation' => 'convoquer',
        'justification' => 'Profil correspond bien.',
    ];

    $analysis = $this->persister->persist($data, $this->jobOffer->id, $this->candidate->id);

    expect($analysis)->toBeInstanceOf(CandidateAnalysis::class);
    expect($analysis->status)->toBe('completed');
    expect($analysis->matching_score)->toBe(78);
    expect($analysis->years_experience)->toBe(5);
    expect($analysis->extracted_skills)->toBe(['PHP', 'Laravel']);
    expect($analysis->recommendation)->toBe(Recommendation::Convoquer);
    expect($analysis->job_offer_id)->toBe($this->jobOffer->id);
    expect($analysis->candidate_id)->toBe($this->candidate->id);
});

test('persist returns existing analysis on duplicate', function () {
    CandidateAnalysis::factory()->create([
        'job_offer_id' => $this->jobOffer->id,
        'candidate_id' => $this->candidate->id,
        'status' => 'completed',
    ]);

    $data = [
        'competences_extraites' => ['PHP'],
        'annees_experience' => 3,
        'niveau_etudes' => 'Bac+3',
        'langues' => ['Français'],
        'matching_score' => 60,
        'points_forts' => ['Test'],
        'lacunes' => [],
        'competences_manquantes' => [],
        'recommandation' => 'attente',
        'justification' => 'Profil moyen.',
    ];

    $result = $this->persister->persist($data, $this->jobOffer->id, $this->candidate->id);

    expect($result->id)->toBe($result->id);
    expect(CandidateAnalysis::count())->toBe(1);
});

test('persist sets correct recommendation enum cast', function () {
    $data = [
        'competences_extraites' => [],
        'annees_experience' => 2,
        'niveau_etudes' => 'Bac',
        'langues' => [],
        'matching_score' => 30,
        'points_forts' => [],
        'lacunes' => ['Manque de compétences'],
        'competences_manquantes' => ['PHP'],
        'recommandation' => 'rejeter',
        'justification' => 'Profil insuffisant.',
    ];

    $analysis = $this->persister->persist($data, $this->jobOffer->id, $this->candidate->id);

    expect($analysis->recommendation)->toBe(Recommendation::Rejeter);
    expect($analysis->recommendation->label())->toBe('À rejeter');
});

test('persist stores array fields as arrays via casts', function () {
    $data = [
        'competences_extraites' => ['PHP', 'MySQL', 'JavaScript'],
        'annees_experience' => 4,
        'niveau_etudes' => 'Bac+2',
        'langues' => ['Français', 'Arabe'],
        'matching_score' => 65,
        'points_forts' => ['Polyvalent'],
        'lacunes' => ['Pas de management'],
        'competences_manquantes' => ['React'],
        'recommandation' => 'attente',
        'justification' => 'Profil acceptable.',
    ];

    $analysis = $this->persister->persist($data, $this->jobOffer->id, $this->candidate->id);

    expect($analysis->extracted_skills)->toBeArray();
    expect($analysis->extracted_skills)->toHaveCount(3);
    expect($analysis->languages)->toBe(['Français', 'Arabe']);
    expect($analysis->strengths)->toBe(['Polyvalent']);
});
