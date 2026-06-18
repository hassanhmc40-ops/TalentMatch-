<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Ai\Concerns\HasConversations;

test('User model uses HasConversations trait', function () {
    $traits = class_uses(User::class);
    expect($traits)->toContain(HasConversations::class);
});

test('User has conversations relationship', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    expect($user->conversations())->toBeInstanceOf(HasMany::class);
});
