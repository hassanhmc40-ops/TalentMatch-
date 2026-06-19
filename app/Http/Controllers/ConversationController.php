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

        // Force a predictable conversation ID tied to this analysis.
        // The SDK's RememberConversation middleware detects a non-null
        // currentConversation() and skips auto-creation, using this ID for
        // all message storage in agent_conversation_messages.
        $conversationId = 'candidate-analysis-'.$analysis->id;

        // continue() sets currentConversation() to our ID and registers the
        // user as the conversation participant — the middleware requires
        // both to activate the memory pipeline.
        $agent = ConversationalAgent::make()
            ->continue($conversationId, auth()->user());

        // prompt() builds an AgentPrompt with instructions, messages, and
        // tools, then invokes the provider gateway. The pipeline is:
        //   RemembersConversations → RememberConversation middleware →
        //   DatabaseConversationStore (stores user + assistant messages)
        $response = $agent->prompt($validated['message']);

        AgentConversation::where('id', $conversationId)
            ->whereNull('candidate_analysis_id')
            ->update(['candidate_analysis_id' => $analysis->id]);

        return redirect()
            ->route('conversations.show', [$offre, $candidat])
            ->withFragment('messages');
    }
}
