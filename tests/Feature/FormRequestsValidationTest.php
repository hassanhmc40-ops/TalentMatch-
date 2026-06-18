<?php

use App\Http\Requests\StoreJobOfferRequest;
use App\Http\Requests\SubmitCandidateRequest;
use App\Models\Candidate;
use App\Models\JobOffer;
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
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => 'Expérience en PHP.', 'offre_id' => $jobOffer->id],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

test('SubmitCandidateRequest rejects empty name', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    $validator = Validator::make(
        ['nom' => '', 'cv_text' => 'Expérience en PHP.', 'offre_id' => $jobOffer->id],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('nom'))->toBeTrue();
});

test('SubmitCandidateRequest rejects empty CV text', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => '', 'offre_id' => $jobOffer->id],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cv_text'))->toBeTrue();
});

test('SubmitCandidateRequest rejects non-existent offre_id', function () {
    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => 'Expérience en PHP.', 'offre_id' => 99999],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('offre_id'))->toBeTrue();
});

test('SubmitCandidateRequest rejects CV text exceeding 50000 characters', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);

    $validator = Validator::make(
        ['nom' => 'Jean Dupont', 'cv_text' => str_repeat('a', 50001), 'offre_id' => $jobOffer->id],
        (new SubmitCandidateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cv_text'))->toBeTrue();
});

test('SubmitCandidateRequest withValidator rejects duplicate candidate name', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    Candidate::factory()->create(['name' => 'Jean Dupont']);

    $request = new SubmitCandidateRequest;
    $request->merge(['nom' => 'Jean Dupont', 'cv_text' => 'Expérience.', 'offre_id' => $jobOffer->id]);

    $validator = Validator::make(
        $request->all(),
        $request->rules()
    );

    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('nom'))->toBeTrue();
});

test('SubmitCandidateRequest withValidator allows different candidate name', function () {
    $jobOffer = JobOffer::factory()->create(['user_id' => $this->user->id]);
    Candidate::factory()->create(['name' => 'Marie Curie']);

    $request = new SubmitCandidateRequest;
    $request->merge(['nom' => 'Jean Dupont', 'cv_text' => 'Expérience.', 'offre_id' => $jobOffer->id]);

    $validator = Validator::make(
        $request->all(),
        $request->rules()
    );

    $request->withValidator($validator);

    expect($validator->passes())->toBeTrue();
});

test('SubmitCandidateRequest messages are in French', function () {
    $messages = (new SubmitCandidateRequest)->messages();

    expect($messages['nom.required'])->toBe('Le nom du candidat est obligatoire.')
        ->and($messages['cv_text.required'])->toBe('Le texte du CV est obligatoire.')
        ->and($messages['offre_id.exists'])->toBe("L'offre d'emploi sélectionnée est invalide.");
});

test('SubmitCandidateRequest attributes returns French field names', function () {
    $attrs = (new SubmitCandidateRequest)->attributes();

    expect($attrs['nom'])->toBe('nom du candidat')
        ->and($attrs['cv_text'])->toBe('texte du CV')
        ->and($attrs['offre_id'])->toBe("offre d'emploi");
});
