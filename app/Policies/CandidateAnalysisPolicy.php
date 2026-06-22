<?php

namespace App\Policies;

use App\Models\CandidateAnalysis;
use App\Models\User;

class CandidateAnalysisPolicy
{
    public function view(User $user, CandidateAnalysis $analysis): bool
    {
        return $user->id === $analysis->jobOffer->user_id;
    }
}
