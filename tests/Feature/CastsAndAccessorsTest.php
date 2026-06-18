<?php

use App\Enums\Recommendation;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;

// ── Recommendation Enum ──

test('toSelectArray returns value => label pairs', function () {
    $select = Recommendation::toSelectArray();

    expect($select)->toBe([
        'convoquer' => 'À convoquer',
        'attente' => 'En attente',
        'rejeter' => 'À rejeter',
    ]);
});

test('fromLabel returns correct enum case for valid labels', function () {
    expect(Recommendation::fromLabel('À convoquer'))->toBe(Recommendation::Convoquer);
    expect(Recommendation::fromLabel('En attente'))->toBe(Recommendation::Attente);
    expect(Recommendation::fromLabel('À rejeter'))->toBe(Recommendation::Rejeter);
});

test('fromLabel throws for invalid label', function () {
    expect(fn () => Recommendation::fromLabel('Invalide'))
        ->toThrow(InvalidArgumentException::class);
});

// ── CandidateAnalysis Accessors ──

test('scoreLevel returns Faible for scores 0 to 30', function () {
    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 0]);
    expect($analysis->scoreLevel())->toBe('Faible');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 15]);
    expect($analysis->scoreLevel())->toBe('Faible');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 30]);
    expect($analysis->scoreLevel())->toBe('Faible');
});

test('scoreLevel returns Moyen for scores 31 to 60', function () {
    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 31]);
    expect($analysis->scoreLevel())->toBe('Moyen');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 45]);
    expect($analysis->scoreLevel())->toBe('Moyen');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 60]);
    expect($analysis->scoreLevel())->toBe('Moyen');
});

test('scoreLevel returns Bon for scores 61 to 80', function () {
    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 61]);
    expect($analysis->scoreLevel())->toBe('Bon');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 70]);
    expect($analysis->scoreLevel())->toBe('Bon');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 80]);
    expect($analysis->scoreLevel())->toBe('Bon');
});

test('scoreLevel returns Excellent for scores 81 to 100', function () {
    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 81]);
    expect($analysis->scoreLevel())->toBe('Excellent');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 95]);
    expect($analysis->scoreLevel())->toBe('Excellent');

    $analysis = CandidateAnalysis::factory()->make(['matching_score' => 100]);
    expect($analysis->scoreLevel())->toBe('Excellent');
});

test('isRecommended returns true for Convoquer', function () {
    $analysis = CandidateAnalysis::factory()->make(['recommendation' => Recommendation::Convoquer]);
    expect($analysis->isRecommended())->toBeTrue();
});

test('isRecommended returns false for Attente and Rejeter', function () {
    $analysis = CandidateAnalysis::factory()->make(['recommendation' => Recommendation::Attente]);
    expect($analysis->isRecommended())->toBeFalse();

    $analysis = CandidateAnalysis::factory()->make(['recommendation' => Recommendation::Rejeter]);
    expect($analysis->isRecommended())->toBeFalse();
});

test('skillCount returns correct count of extracted skills', function () {
    $analysis = CandidateAnalysis::factory()->make(['extracted_skills' => ['PHP', 'Laravel', 'MySQL']]);
    expect($analysis->skillCount())->toBe(3);

    $analysis = CandidateAnalysis::factory()->make(['extracted_skills' => []]);
    expect($analysis->skillCount())->toBe(0);

    $analysis = CandidateAnalysis::factory()->make(['extracted_skills' => ['PHP']]);
    expect($analysis->skillCount())->toBe(1);
});

test('missingSkillCount returns correct count of missing skills', function () {
    $analysis = CandidateAnalysis::factory()->make(['missing_skills' => ['Docker', 'Kubernetes']]);
    expect($analysis->missingSkillCount())->toBe(2);

    $analysis = CandidateAnalysis::factory()->make(['missing_skills' => []]);
    expect($analysis->missingSkillCount())->toBe(0);

    $analysis = CandidateAnalysis::factory()->make(['missing_skills' => ['React']]);
    expect($analysis->missingSkillCount())->toBe(1);
});

// ── JobOffer Accessors ──

test('JobOffer skillCount returns correct count of required skills', function () {
    $offer = JobOffer::factory()->make(['required_skills' => ['PHP', 'MySQL', 'Laravel']]);
    expect($offer->skillCount())->toBe(3);

    $offer = JobOffer::factory()->make(['required_skills' => []]);
    expect($offer->skillCount())->toBe(0);

    $offer = JobOffer::factory()->make(['required_skills' => null]);
    expect($offer->skillCount())->toBe(0);

    $offer = JobOffer::factory()->make(['required_skills' => ['PHP']]);
    expect($offer->skillCount())->toBe(1);
});
