<?php

namespace App\Actions;

use App\Models\CandidateAnalysis;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersistValidatedAnalysis
{
    public function __construct(
        private readonly ValidateStructuredAnalysis $validator,
    ) {}

    public function persist(array $validatedData, int $jobOfferId, int $candidateId): CandidateAnalysis
    {
        $mapped = $this->validator->getKeyMapping();

        $englishData = [];
        foreach ($validatedData as $key => $value) {
            $englishKey = $mapped[$key] ?? $key;
            $englishData[$englishKey] = $value;
        }

        $existing = CandidateAnalysis::query()
            ->where('job_offer_id', $jobOfferId)
            ->where('candidate_id', $candidateId)
            ->exists();

        if ($existing) {
            Log::warning('Tentative d\'analyse en double ignorée.', [
                'job_offer_id' => $jobOfferId,
                'candidate_id' => $candidateId,
            ]);

            return CandidateAnalysis::query()
                ->where('job_offer_id', $jobOfferId)
                ->where('candidate_id', $candidateId)
                ->firstOrFail();
        }

        try {
            return DB::transaction(function () use ($englishData, $jobOfferId, $candidateId) {
                return CandidateAnalysis::create([
                    ...$englishData,
                    'job_offer_id' => $jobOfferId,
                    'candidate_id' => $candidateId,
                    'status' => 'completed',
                ]);
            });
        } catch (UniqueConstraintViolationException $e) {
            Log::warning('Contrainte d\'unicité violée lors de la persistance de l\'analyse.', [
                'job_offer_id' => $jobOfferId,
                'candidate_id' => $candidateId,
            ]);

            return CandidateAnalysis::query()
                ->where('job_offer_id', $jobOfferId)
                ->where('candidate_id', $candidateId)
                ->firstOrFail();
        }
    }
}
