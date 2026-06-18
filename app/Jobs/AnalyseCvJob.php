<?php

namespace App\Jobs;

use App\Actions\PersistValidatedAnalysis;
use App\Actions\ValidateStructuredAnalysis;
use App\Ai\Agents\CvAnalysisAgent;
use App\Exceptions\ValidationFailedException;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyseCvJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30];

    public function __construct(
        public readonly int $candidateId,
        public readonly int $jobOfferId,
    ) {}

    public function handle(
        ValidateStructuredAnalysis $validator,
        PersistValidatedAnalysis $persister,
    ): void {
        $candidate = Candidate::findOrFail($this->candidateId);
        $jobOffer = JobOffer::findOrFail($this->jobOfferId);

        $prompt = sprintf(
            "Analysez le CV suivant pour l'offre d'emploi ci-dessous et retournez l'analyse au format JSON structuré.\n\nCV du candidat :\n%s\n\nOffre d'emploi :\nTitre : %s\nDescription : %s\nCompétences requises : %s\nExpérience minimale : %d ans",
            $candidate->cv_text,
            $jobOffer->title,
            $jobOffer->description,
            implode(', ', $jobOffer->required_skills ?? []),
            $jobOffer->min_experience_years,
        );

        try {
            $agent = CvAnalysisAgent::make();
            $response = $agent->prompt($prompt);

            $validated = $validator->validate($response->toArray());

            $persister->persist($validated, $this->jobOfferId, $this->candidateId);
        } catch (ValidationFailedException $e) {
            Log::error('Analyse CV : la validation de la réponse IA a échoué.', [
                'candidate_id' => $this->candidateId,
                'job_offer_id' => $this->jobOfferId,
                'errors' => $e->errors,
            ]);

            CandidateAnalysis::query()
                ->where('job_offer_id', $this->jobOfferId)
                ->where('candidate_id', $this->candidateId)
                ->update(['status' => 'failed']);

            $this->fail($e);
        } catch (\Throwable $e) {
            Log::warning('Analyse CV : erreur temporaire lors de l\'appel IA.', [
                'candidate_id' => $this->candidateId,
                'job_offer_id' => $this->jobOfferId,
                'error' => $e->getMessage(),
            ]);

            $this->release($this->backoff[0]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Analyse CV : la tâche a échoué après tous les essais.', [
            'candidate_id' => $this->candidateId,
            'job_offer_id' => $this->jobOfferId,
            'error' => $e->getMessage(),
        ]);

        CandidateAnalysis::query()
            ->where('job_offer_id', $this->jobOfferId)
            ->where('candidate_id', $this->candidateId)
            ->update(['status' => 'failed']);
    }
}
