<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Database\Seeder;

class CandidateAnalysisSeeder extends Seeder
{
    public function run(): void
    {
        $offers = JobOffer::all();
        $candidates = Candidate::all();

        if ($offers->isEmpty() || $candidates->isEmpty()) {
            return;
        }

        foreach ($offers as $offer) {
            foreach ($candidates->random(min(3, $candidates->count())) as $candidate) {
                CandidateAnalysis::factory()->create([
                    'job_offer_id' => $offer->id,
                    'candidate_id' => $candidate->id,
                ]);
            }
        }
    }
}
