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

        $fewShotExample = "Voici un exemple de CV et d'offre d'emploi avec la sortie JSON attendue :\n\n--- Exemple ---\nCV :\n\"Développeur PHP avec 3 ans d'expérience chez WebAgency. Compétences : PHP, MySQL, JavaScript, Laravel. Anglais courant. Bac+5 en informatique.\"\n\nOffre :\nTitre : Développeur Backend PHP\nDescription : Développement d'applications web en PHP et Laravel\nCompétences requises : PHP, Laravel, MySQL, Docker\nExpérience minimale : 2 ans\n\nSortie JSON attendue :\n{\n  \"competences_extraites\": [\"PHP\", \"MySQL\", \"JavaScript\", \"Laravel\"],\n  \"annees_experience\": 3,\n  \"niveau_etudes\": \"Bac+5\",\n  \"langues\": [\"Anglais\"],\n  \"matching_score\": 65,\n  \"points_forts\": [\"Maîtrise de PHP, Laravel et MySQL\", \"Expérience correspond au minimum requis\"],\n  \"lacunes\": [\"Pas d'expérience avec Docker\"],\n  \"competences_manquantes\": [\"Docker\"],\n  \"recommandation\": \"attente\",\n  \"justification\": \"Le candidat maîtrise 3 des 4 compétences requises et dépasse l'expérience minimale, mais Docker est manquant. Score de 65/100.\"\n}\n--- Fin de l'exemple ---\n\nAnalyse maintenant le CV suivant pour l'offre d'emploi ci-dessous et retourne l'analyse au format JSON structuré.\n";

        $prompt = $fewShotExample.sprintf(
            "CV du candidat :\n%s\n\nOffre d'emploi :\nTitre : %s\nDescription : %s\nCompétences requises : %s\nExpérience minimale : %d ans",
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
