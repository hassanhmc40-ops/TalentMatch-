<?php

use App\Http\Requests\StoreJobOfferRequest;
use App\Http\Requests\SubmitCandidateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

// ── StoreJobOfferRequest ──

test('StoreJobOfferRequest authorize returns true for authenticated user', function () {
    actingAs($this->user);

    $request = new StoreJobOfferRequest;
    $request->setUserResolver(fn () => $this->user);

    expect($request->authorize())->toBeTrue();
});

test('StoreJobOfferRequest authorize returns false for guest', function () {
    $request = new StoreJobOfferRequest;
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

test('StoreJobOfferRequest validates trimmed whitespace title fails required', function () {
    actingAs($this->user);

    $response = $this->post(route('offres.store'), [
        'title' => '   ',
        'description' => 'Description valide pour le test.',
        'required_skills' => ['PHP'],
        'min_experience_years' => 0,
    ]);

    $response->assertSessionHasErrors(['title' => 'Le titre est obligatoire.']);
});

test('StoreJobOfferRequest filters empty skills from validation', function () {
    actingAs($this->user);

    $response = $this->post(route('offres.store'), [
        'title' => 'Titre valide',
        'description' => 'Description valide pour le test de validation des compétences.',
        'required_skills' => ['PHP', '', 'MySQL'],
        'min_experience_years' => 0,
    ]);

    $response->assertSessionMissing('required_skills');
    $response->assertSessionHasNoErrors();
});

test('StoreJobOfferRequest attributes returns French field names', function () {
    $attrs = (new StoreJobOfferRequest)->attributes();

    expect($attrs['title'])->toBe('titre')
        ->and($attrs['description'])->toBe('description')
        ->and($attrs['required_skills'])->toBe('compétences')
        ->and($attrs['min_experience_years'])->toBe("années d'expérience");
});

// ── SubmitCandidateRequest ──

test('SubmitCandidateRequest accepts valid submission data', function () {
    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => 'Expérience en PHP.'],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

test('SubmitCandidateRequest rejects empty name', function () {
    $validator = Validator::make(
        ['nom' => '', 'cv_text' => 'Expérience en PHP.'],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('nom'))->toBeTrue();
});

test('SubmitCandidateRequest rejects empty CV text', function () {
    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => ''],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cv_text'))->toBeTrue();
});

test('SubmitCandidateRequest rejects CV text exceeding 50000 characters', function () {
    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => str_repeat('a', 50001)],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cv_text'))->toBeTrue();
});

test('SubmitCandidateRequest messages are in French', function () {
    $messages = (new SubmitCandidateRequest)->messages();

    expect($messages['nom.required'])->toBe('Le nom du candidat est obligatoire.')
        ->and($messages['cv_text.required'])->toBe('Le texte du CV est obligatoire.');
});

test('SubmitCandidateRequest attributes returns French field names', function () {
    $attrs = (new SubmitCandidateRequest)->attributes();

    expect($attrs['nom'])->toBe('nom du candidat')
        ->and($attrs['cv_text'])->toBe('texte du CV');
});
