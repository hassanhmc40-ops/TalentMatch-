<?php

namespace App\Http\Controllers;

use App\Ai\Agents\ConversationalAgent;
use App\Models\AgentConversation;
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

        $agent = ConversationalAgent::make()
            ->continue($conversationId, auth()->user());

        $response = $agent->prompt($validated['message']);

        AgentConversation::where('id', $conversationId)
            ->whereNull('candidate_analysis_id')
            ->update(['candidate_analysis_id' => $analysis->id]);

        return redirect()
            ->route('conversations.show', [$offre, $candidat])
            ->withFragment('messages');
    }
}
