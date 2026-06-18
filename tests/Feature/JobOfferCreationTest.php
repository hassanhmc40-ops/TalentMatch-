<?php

use App\Models\JobOffer;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

test('authenticated user can view the creation form', function () {
    actingAs($this->user)
        ->get(route('offres.create'))
        ->assertOk()
        ->assertSeeText('Titre de l\'offre')
        ->assertSeeText('Description')
        ->assertSeeText('Compétences requises')
        ->assertSeeText('Années d\'expérience minimum')
        ->assertSeeText("Créer l'offre");
});

test('unauthenticated user is redirected to login', function () {
    get(route('offres.create'))
        ->assertRedirect(route('login'));
});

test('successful creation stores job offer with correct data', function () {
    actingAs($this->user);

    post(route('offres.store'), [
        'title' => 'Développeur PHP',
        'description' => 'Nous recherchons un développeur PHP expérimenté pour rejoindre notre équipe.',
        'required_skills' => ['PHP', 'Laravel', 'MySQL'],
        'min_experience_years' => 3,
    ])->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', "L'offre d'emploi a été créée avec succès.");

    assertDatabaseHas('job_offers', [
        'user_id' => $this->user->id,
        'title' => 'Développeur PHP',
        'min_experience_years' => 3,
    ]);

    $offer = JobOffer::where('title', 'Développeur PHP')->first();
    expect($offer->required_skills)->toBe(['PHP', 'Laravel', 'MySQL']);
});

test('created offer user_id is set from auth id not from request', function () {
    $otherUser = User::factory()->create();

    actingAs($this->user)->post(route('offres.store'), [
        'title' => 'Développeur PHP',
        'description' => 'Nous recherchons un développeur PHP expérimenté pour rejoindre notre équipe.',
        'required_skills' => ['PHP'],
        'min_experience_years' => 2,
        'user_id' => $otherUser->id,
    ]);

    assertDatabaseHas('job_offers', [
        'title' => 'Développeur PHP',
        'user_id' => $this->user->id,
    ]);
});

test('validation errors for missing title', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'description' => 'Description valide pour le test.',
            'required_skills' => ['PHP'],
            'min_experience_years' => 0,
        ])
        ->assertSessionHasErrors(['title' => 'Le titre est obligatoire.']);
});

test('validation errors for short description', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => 'Développeur PHP',
            'description' => 'Court',
            'required_skills' => ['PHP'],
            'min_experience_years' => 0,
        ])
        ->assertSessionHasErrors(['description']);
});

test('validation errors for empty required skills', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => 'Développeur PHP',
            'description' => 'Description valide pour le test de validation.',
            'required_skills' => [],
            'min_experience_years' => 0,
        ])
        ->assertSessionHasErrors(['required_skills' => 'Au moins une compétence est requise.']);
});

test('validation errors for duplicate skills', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => 'Développeur PHP',
            'description' => 'Description valide pour le test de validation.',
            'required_skills' => ['PHP', 'PHP'],
            'min_experience_years' => 0,
        ])
        ->assertSessionHasErrors(['required_skills.0']);
});

test('validation errors for negative minimum experience', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => 'Développeur PHP',
            'description' => 'Description valide pour le test de validation.',
            'required_skills' => ['PHP'],
            'min_experience_years' => -1,
        ])
        ->assertSessionHasErrors(['min_experience_years']);
});

test('validation errors for excessive minimum experience', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => 'Développeur PHP',
            'description' => 'Description valide pour le test de validation.',
            'required_skills' => ['PHP'],
            'min_experience_years' => 51,
        ])
        ->assertSessionHasErrors(['min_experience_years']);
});

test('validation errors for title exceeding max length', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => str_repeat('a', 256),
            'description' => 'Description valide pour le test de validation.',
            'required_skills' => ['PHP'],
            'min_experience_years' => 0,
        ])
        ->assertSessionHasErrors(['title']);
});

test('required skills are stored as json and return as php array', function () {
    actingAs($this->user);

    post(route('offres.store'), [
        'title' => 'Développeur PHP',
        'description' => 'Nous recherchons un développeur PHP expérimenté pour rejoindre notre équipe.',
        'required_skills' => ['PHP', 'Laravel'],
        'min_experience_years' => 2,
    ]);

    $offer = JobOffer::where('title', 'Développeur PHP')->first();
    expect($offer->required_skills)->toBeArray();
    expect($offer->required_skills)->toEqual(['PHP', 'Laravel']);
});

test('no job offer is created when validation fails', function () {
    actingAs($this->user)
        ->post(route('offres.store'), [
            'title' => '',
            'description' => 'Court',
            'required_skills' => [],
            'min_experience_years' => -1,
        ]);

    assertDatabaseCount('job_offers', 0);
});
