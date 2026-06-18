<?php

use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders sidebar with navigation links', function () {
    $view = $this->blade(
        '<x-sidebar />',
    );

    $view->assertSee('Tableau de bord');
    $view->assertSee('Mes offres');
    $view->assertSee('Candidats');
    $view->assertSee('TalentMatch');
});

it('renders sidebar collapse button', function () {
    $view = $this->blade(
        '<x-sidebar />',
    );

    $view->assertSee('Réduire');
});

it('renders kpi card with icon, value, and label', function () {
    $view = $this->blade(
        '<x-kpi-card icon="offres" :value="5" label="Offres d\'emploi" color="primary" />',
    );

    $view->assertSee('5');
    $view->assertSee("Offres d'emploi");
});

it('renders kpi card with different color variants', function () {
    $colors = ['primary', 'success', 'warning', 'danger'];

    foreach ($colors as $color) {
        $view = $this->blade(
            '<x-kpi-card icon="offres" :value="3" label="Test" color="'.$color.'" />',
        );

        $view->assertSee('3');
        $view->assertSee('Test');
    }
});

it('renders kpi card with zero value', function () {
    $view = $this->blade(
        '<x-kpi-card :value="0" label="Zéro" />',
    );

    $view->assertSee('0');
    $view->assertSee('Zéro');
});

it('displays dashboard page with kpi cards', function () {
    $offres = JobOffer::factory()
        ->count(3)
        ->for($this->user)
        ->create();

    $candidates = CandidateAnalysis::factory()
        ->count(4)
        ->sequence(
            ['status' => 'completed', 'matching_score' => 75],
            ['status' => 'completed', 'matching_score' => 85],
            ['status' => 'completed', 'matching_score' => 65],
            ['status' => 'pending', 'matching_score' => 0],
        )
        ->create();

    $offres->each(fn ($o) => $candidates->each(fn ($c) => $c->update(['job_offer_id' => $o->id])));

    Cache::flush();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Tableau de bord')
        ->assertSee('3')         // totalOffers
        ->assertSee('3')         // analyzedCandidates (3 completed)
        ->assertSee('75')        // avgScore ((75+85+65)/3 = 75)
        ->assertSee('1');        // pendingAnalyses
});

it('displays dashboard page with zero values when no data exists', function () {
    Cache::flush();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Tableau de bord')
        ->assertSee('0');
});

it('scopes kpi data to authenticated user only', function () {
    $otherUser = User::factory()->create();

    JobOffer::factory()
        ->count(5)
        ->for($otherUser)
        ->create();

    Cache::flush();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('0');        // totalOffers = 0 for this user
});
