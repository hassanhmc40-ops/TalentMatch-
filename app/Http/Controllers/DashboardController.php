<?php

namespace App\Http\Controllers;

use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $userId = auth()->id();

        $kpi = Cache::remember("dashboard.kpi.{$userId}", 300, function () use ($userId) {
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

        return view('dashboard', $kpi);
    }
}
