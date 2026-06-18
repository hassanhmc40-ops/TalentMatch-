<?php

namespace App\Http\Controllers;

use App\Ai\Agents\ConversationalAgent;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function show(JobOffer $offre, Candidate $candidat)
    {
        Gate::authorize('view', $offre);

        $analysis = CandidateAnalysis::query()
            ->where('job_offer_id', $offre->id)
            ->where('candidate_id', $candidat->id)
            ->firstOrFail();

        $conversationId = 'candidate-analysis-'.$analysis->id;

        return view('conversations.show', compact('offre', 'candidat', 'analysis', 'conversationId'));
    }

    public function store(Request $request, JobOffer $offre, Candidate $candidat)
    {
        Gate::authorize('view', $offre);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $analysis = CandidateAnalysis::query()
            ->where('job_offer_id', $offre->id)
            ->where('candidate_id', $candidat->id)
            ->firstOrFail();

        $conversationId = 'candidate-analysis-'.$analysis->id;
        $systemContext = sprintf(
            "Contexte : Le recruteur consulte l'analyse du candidat '%s' pour l'offre '%s'. Score : %d/100. Recommandation : %s.",
            $candidat->name,
            $offre->title,
            $analysis->matching_score,
            $analysis->recommendation->value,
        );

        $agent = ConversationalAgent::make()
            ->conversation($conversationId)
            ->systemContext($systemContext);

        $response = $agent->send($validated['message']);

        return redirect()
            ->route('conversations.show', [$offre, $candidat])
            ->withFragment('messages');
    }
}
