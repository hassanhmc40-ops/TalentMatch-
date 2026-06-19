<?php

use App\Enums\Recommendation;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;

it('renders convoquer badge', function () {
    $view = $this->blade(
        '<x-recommendation-badge :recommendation="$r" />',
        ['r' => Recommendation::Convoquer],
    );

    $view->assertSee('À convoquer');
});

it('renders attente badge', function () {
    $view = $this->blade(
        '<x-recommendation-badge :recommendation="$r" />',
        ['r' => Recommendation::Attente],
    );

    $view->assertSee('En attente');
});

it('renders rejeter badge', function () {
    $view = $this->blade(
        '<x-recommendation-badge :recommendation="$r" />',
        ['r' => Recommendation::Rejeter],
    );

    $view->assertSee('À rejeter');
});

it('renders neutral badge for null recommendation', function () {
    $view = $this->blade(
        '<x-recommendation-badge :recommendation="$r" />',
        ['r' => null],
    );

    $view->assertSee('Non définie');
});

it('renders callout heading and justification', function () {
    $view = $this->blade(
        '<x-recommendation-callout :recommendation="$r" justification="Test justification" />',
        ['r' => Recommendation::Convoquer],
    );

    $view->assertSee('À convoquer');
    $view->assertSee('Test justification');
});

it('renders callout with attente', function () {
    $view = $this->blade(
        '<x-recommendation-callout :recommendation="$r" justification="En attente de décision" />',
        ['r' => Recommendation::Attente],
    );

    $view->assertSee('En attente');
    $view->assertSee('En attente de décision');
});

it('renders callout with rejeter', function () {
    $view = $this->blade(
        '<x-recommendation-callout :recommendation="$r" justification="Profil non adapté" />',
        ['r' => Recommendation::Rejeter],
    );

    $view->assertSee('À rejeter');
    $view->assertSee('Profil non adapté');
});

it('shows recommendation stats card on offer detail page', function () {
    $user = User::factory()->create();
    $offer = JobOffer::factory()->for($user)->create();

    CandidateAnalysis::factory()->count(2)->create([
        'job_offer_id' => $offer->id,
        'recommendation' => Recommendation::Convoquer,
    ]);

    CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'recommendation' => Recommendation::Attente,
    ]);

    CandidateAnalysis::factory()->create([
        'job_offer_id' => $offer->id,
        'recommendation' => Recommendation::Rejeter,
    ]);

    $this->actingAs($user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertSee('Répartition des recommandations')
        ->assertSee('2')
        ->assertSee('1');
});

it('shows zero counts when offer has no analyses', function () {
    $user = User::factory()->create();
    $offer = JobOffer::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('offres.show', $offer))
        ->assertOk()
        ->assertDontSee('Répartition des recommandations');
});
