<?php

use App\Enums\Recommendation;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

test('scopeRanked orders by matching_score descending', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $low = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 30,
    ]);
    $high = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 90,
    ]);
    $mid = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 60,
    ]);

    $results = CandidateAnalysis::where('job_offer_id', $offer->id)
        ->ranked()
        ->get();

    expect($results[0]->matching_score)->toBe(90);
    expect($results[1]->matching_score)->toBe(60);
    expect($results[2]->matching_score)->toBe(30);
});

test('applyTieBreakers sorts by years_experience when scores are equal', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $a = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 2,
    ]);
    $b = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
    ]);
    $c = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 8,
    ]);

    $results = CandidateAnalysis::applyTieBreakers(
        CandidateAnalysis::where('job_offer_id', $offer->id)->ranked()->get()
    );

    expect($results[0]->years_experience)->toBe(8);
    expect($results[1]->years_experience)->toBe(5);
    expect($results[2]->years_experience)->toBe(2);
});

test('applyTieBreakers sorts by skills count when score and experience are equal', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $a = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
        'extracted_skills' => ['PHP', 'Laravel'],
    ]);
    $b = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
        'extracted_skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript'],
    ]);
    $c = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
        'extracted_skills' => ['PHP'],
    ]);

    $results = CandidateAnalysis::applyTieBreakers(
        CandidateAnalysis::where('job_offer_id', $offer->id)->ranked()->get()
    );

    expect($results[0]->skillCount())->toBe(4);
    expect($results[1]->skillCount())->toBe(2);
    expect($results[2]->skillCount())->toBe(1);
});

test('applyTieBreakers sorts by education level as final fallback', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $a = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
        'extracted_skills' => ['PHP'],
        'education_level' => 'Bac',
    ]);
    $b = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
        'extracted_skills' => ['PHP'],
        'education_level' => 'Bac+5',
    ]);
    $c = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 75,
        'years_experience' => 5,
        'extracted_skills' => ['PHP'],
        'education_level' => 'Bac+2',
    ]);

    $results = CandidateAnalysis::applyTieBreakers(
        CandidateAnalysis::where('job_offer_id', $offer->id)->ranked()->get()
    );

    expect($results[0]->education_level)->toBe('Bac+5');
    expect($results[1]->education_level)->toBe('Bac+2');
    expect($results[2]->education_level)->toBe('Bac');
});

test('offer detail page shows ranked leaderboard with correct order', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    $last = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 30,
        'recommendation' => Recommendation::Rejeter,
    ]);
    $first = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 90,
        'recommendation' => Recommendation::Convoquer,
    ]);
    $second = CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 60,
        'recommendation' => Recommendation::Attente,
    ]);

    actingAs($this->user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertSeeText('Classement des candidats')
        ->assertSeeInOrder([
            $first->candidate->name,
            $second->candidate->name,
            $last->candidate->name,
        ]);
});

test('offer detail page shows empty state when no candidates', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertSeeText('Aucun candidat analysé pour cette offre.');
});

test('score progress bar shows with correct color classes', function () {
    $offer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 85,
    ]);
    CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 50,
    ]);
    CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'matching_score' => 25,
    ]);

    $response = actingAs($this->user)->get(route('offres.show', $offer));

    $response->assertOk();
    $response->assertSee('Classement des candidats');
    expect($response->content())->toMatch('/bg-success-500/');
    expect($response->content())->toMatch('/bg-warning-500/');
    expect($response->content())->toMatch('/bg-danger-500/');
});

test('educationWeight returns correct values for known levels', function () {
    expect(CandidateAnalysis::educationWeight('Doctorat'))->toBe(100);
    expect(CandidateAnalysis::educationWeight('Bac+5'))->toBe(80);
    expect(CandidateAnalysis::educationWeight('Bac+3'))->toBe(60);
    expect(CandidateAnalysis::educationWeight('Bac+2'))->toBe(40);
    expect(CandidateAnalysis::educationWeight('Bac'))->toBe(20);
    expect(CandidateAnalysis::educationWeight(''))->toBe(0);
    expect(CandidateAnalysis::educationWeight(null))->toBe(0);
});
