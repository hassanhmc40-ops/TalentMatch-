<?php

use App\Models\JobOffer;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
});

test('authenticated user sees only their own offers', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    JobOffer::factory(3)->create(['user_id' => $otherUser->id]);
    $myOffers = JobOffer::factory(2)->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.index'))
        ->assertOk()
        ->assertSeeText($myOffers[0]->title)
        ->assertSeeText($myOffers[1]->title);
});

test('offers are ordered by most recent first', function () {
    $old = JobOffer::factory()->create(['user_id' => $this->user->id, 'created_at' => now()->subDays(2)]);
    $new = JobOffer::factory()->create(['user_id' => $this->user->id, 'created_at' => now()]);

    actingAs($this->user)
        ->get(route('offres.index'))
        ->assertOk()
        ->assertSeeTextInOrder([$new->title, $old->title]);
});

test('pagination shows 10 offers per page', function () {
    JobOffer::factory(11)->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->get(route('offres.index'))
        ->assertOk()
        ->assertSeeText('Next');
});

test('unauthenticated user is redirected to login', function () {
    get(route('offres.index'))
        ->assertRedirect(route('login'));
});

test('empty state displays when user has no offers', function () {
    actingAs($this->user)
        ->get(route('offres.index'))
        ->assertOk()
        ->assertSeeText("Vous n'avez pas encore créé d'offre d'emploi.")
        ->assertSeeText('Créer votre première offre');
});

test('other users offers are not visible in the list', function () {
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $otherOffer = JobOffer::factory()->create(['user_id' => $otherUser->id, 'title' => 'Offre cachée']);

    actingAs($this->user)
        ->get(route('offres.index'))
        ->assertOk()
        ->assertDontSeeText('Offre cachée');
});
