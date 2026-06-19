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
use Laravel\Ai\Streaming\Events\ToolCall;
use Laravel\Ai\Streaming\Events\ToolResult;
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
            ->continue($conversationId, auth()->user())
            ->setContext($this->buildContext($offre, $candidat, $analysis));

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
            ->continue($conversationId, auth()->user())
            ->setContext($this->buildContext($offre, $candidat, $analysis));

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

                if ($event instanceof ToolCall) {
                    $data = json_encode([
                        'type' => 'tool_call',
                        'tool_name' => $event->toolCall->name,
                        'arguments' => $event->toolCall->arguments,
                    ]);
                    echo "data: {$data}\n\n";
                    ob_flush();
                    flush();
                }

                if ($event instanceof ToolResult) {
                    $data = json_encode([
                        'type' => 'tool_result',
                        'tool_name' => $event->toolResult->name,
                        'successful' => $event->successful,
                        'error' => $event->error,
                    ]);
                    echo "data: {$data}\n\n";
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

    private function buildContext(JobOffer $offre, Candidate $candidat, CandidateAnalysis $analysis): array
    {
        $score = $analysis->matching_score;

        $scoreLevel = match (true) {
            $score >= 81 => 'Excellent',
            $score >= 61 => 'Bon',
            $score >= 31 => 'Moyen',
            default => 'Faible',
        };

        $keySkills = [];
        if ($analysis->extracted_skills) {
            $skills = json_decode($analysis->extracted_skills, true);
            if (is_array($skills)) {
                $keySkills = $skills;
            }
        }

        return [
            'candidate_name' => $candidat->name,
            'candidat_id' => $candidat->id,
            'offer_title' => $offre->title,
            'offre_id' => $offre->id,
            'analyse_id' => $analysis->id,
            'matching_score' => $score,
            'score_level' => $scoreLevel,
            'recommendation' => $analysis->recommendation,
            'key_skills' => $keySkills,
        ];
    }
}
