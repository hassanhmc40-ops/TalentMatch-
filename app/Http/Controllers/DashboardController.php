<?php

namespace App\Http\Controllers;

use App\Models\AgentConversation;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $userId = auth()->id();

        $cacheKey = "dashboard.kpi.{$userId}";
        $previousKey = "dashboard.kpi.previous.{$userId}";

        $kpi = Cache::remember($cacheKey, 300, function () use ($userId) {
            $totalOffers = JobOffer::query()
                ->where('user_id', $userId)
                ->count();

            $analyzedCandidates = CandidateAnalysis::query()
                ->whereHas('jobOffer', fn ($q) => $q->where('user_id', $userId))
                ->where('status', 'completed')
                ->count();

            $avgScore = CandidateAnalysis::query()
                ->whereHas('jobOffer', fn ($q) => $q->where('user_id', $userId))
                ->where('status', 'completed')
                ->avg('matching_score');

            $pendingAnalyses = CandidateAnalysis::query()
                ->whereHas('jobOffer', fn ($q) => $q->where('user_id', $userId))
                ->where('status', 'pending')
                ->count();

            return [
                'totalOffers' => $totalOffers,
                'analyzedCandidates' => $analyzedCandidates,
                'avgScore' => $avgScore ? round($avgScore) : 0,
                'pendingAnalyses' => $pendingAnalyses,
            ];
        });

        $previous = Cache::get($previousKey);

        $trends = [
            'totalOffers' => $this->computeTrend($kpi['totalOffers'], $previous['totalOffers'] ?? null),
            'analyzedCandidates' => $this->computeTrend($kpi['analyzedCandidates'], $previous['analyzedCandidates'] ?? null),
            'avgScore' => $this->computeTrend($kpi['avgScore'], $previous['avgScore'] ?? null),
            'pendingAnalyses' => $this->computeTrend($kpi['pendingAnalyses'], $previous['pendingAnalyses'] ?? null),
        ];

        Cache::put($previousKey, $kpi, 600);

        $bands = [0, 0, 0, 0];
        $completedAnalyses = CandidateAnalysis::query()
            ->whereHas('jobOffer', fn ($q) => $q->where('user_id', $userId))
            ->where('status', 'completed')
            ->pluck('matching_score');

        foreach ($completedAnalyses as $score) {
            if ($score >= 81) {
                $bands[3]++;
            } elseif ($score >= 61) {
                $bands[2]++;
            } elseif ($score >= 31) {
                $bands[1]++;
            } else {
                $bands[0]++;
            }
        }

        $analysesQuery = CandidateAnalysis::query()
            ->whereHas('jobOffer', fn ($q) => $q->where('user_id', $userId))
            ->with('candidate', 'jobOffer');

        $failedAnalyses = (clone $analysesQuery)
            ->where('status', 'failed')
            ->count();

        $recentAnalyses = (clone $analysesQuery)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $analysesByStatus = [
            'all' => $recentAnalyses,
            'completed' => (clone $analysesQuery)->where('status', 'completed')->orderByDesc('created_at')->take(10)->get(),
            'pending' => (clone $analysesQuery)->where('status', 'pending')->orderByDesc('created_at')->take(10)->get(),
            'failed' => (clone $analysesQuery)->where('status', 'failed')->orderByDesc('created_at')->take(10)->get(),
        ];

        $recentConversations = AgentConversation::query()
            ->forDashboard($userId)
            ->get()
            ->map(fn ($conv) => [
                'id' => $conv->id,
                'title' => $conv->title,
                'candidate_name' => $conv->candidateAnalysis?->candidate?->name ?? 'Inconnu',
                'offer_title' => $conv->candidateAnalysis?->jobOffer?->title ?? 'Inconnue',
                'message_count' => $conv->messages_count,
                'last_message' => $conv->latestMessage?->content ? Str::limit(strip_tags($conv->latestMessage->content), 80) : '',
                'last_activity' => $conv->updated_at->diffForHumans(),
                'updated_at' => $conv->updated_at->toIso8601String(),
                'url' => $conv->candidateAnalysis ? route('conversations.show', [$conv->candidateAnalysis->jobOffer, $conv->candidateAnalysis->candidate]) : '#',
            ])
            ->values();

        return view('dashboard', [
            ...$kpi,
            'trends' => $trends,
            'scoreBands' => $bands,
            'scoreLabels' => ['0-30', '31-60', '61-80', '81-100'],
            'scoreColors' => ['danger', 'warning', 'primary', 'success'],
            'analysesByStatus' => $analysesByStatus,
            'failedAnalyses' => $failedAnalyses,
            'recentConversations' => $recentConversations,
        ]);
    }

    protected function computeTrend(int $current, ?int $previous): string
    {
        if ($previous === null || $current === $previous) {
            return 'neutral';
        }

        return $current > $previous ? 'up' : 'down';
    }
}
