<?php

namespace App\Http\Controllers;

use App\Ai\Agents\ConversationalAgent;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Ai\Streaming\Events\TextDelta;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $messages = AgentConversationMessage::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'role' => $msg->role,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toIso8601String(),
            ])
            ->values();

        return view('conversations.show', compact('offre', 'candidat', 'analysis', 'conversationId', 'messages'));
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $response->text,
            ]);
        }

        return redirect()
            ->route('conversations.show', [$offre, $candidat])
            ->withFragment('messages');
    }

    public function stream(Request $request, JobOffer $offre, Candidate $candidat)
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

        $response = new StreamedResponse(function () use ($agent, $validated, $conversationId, $analysis) {
            header('X-Accel-Buffering: no');
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');

            $stream = $agent->stream($validated['message']);

            foreach ($stream as $event) {
                if ($event instanceof TextDelta) {
                    $token = json_encode(['token' => $event->delta]);
                    echo "data: {$token}\n\n";
                    ob_flush();
                    flush();
                }
            }

            AgentConversation::where('id', $conversationId)
                ->whereNull('candidate_analysis_id')
                ->update(['candidate_analysis_id' => $analysis->id]);

            echo "data: {\"done\": true}\n\n";
            ob_flush();
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
