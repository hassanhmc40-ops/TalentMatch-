<?php

namespace App\Policies;

use App\Models\AgentConversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, AgentConversation $conversation): bool
    {
        return $user->id === $conversation->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }
}
