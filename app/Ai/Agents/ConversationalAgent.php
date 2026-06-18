<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class ConversationalAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): Stringable|string
    {
        return 'You are an HR assistant specialized in candidate analysis. Use the available tools to retrieve real data from the database. Never invent scores, missing skills, job requirements, or comparison results. Always use tools to get accurate information.';
    }

    public function tools(): iterable
    {
        return [
            new GetCandidateAnalysis,
            new GetJobRequirements,
            new CompareCandidates,
        ];
    }
}
