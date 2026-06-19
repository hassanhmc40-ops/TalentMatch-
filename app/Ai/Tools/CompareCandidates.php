<?php

namespace App\Ai\Tools;

use App\Models\CandidateAnalysis;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CompareCandidates implements Tool
{
    private const MATCHING_SCORE_WEIGHT = 0.40;

    private const EXPERIENCE_WEIGHT = 0.25;

    private const SKILLS_WEIGHT = 0.25;

    private const EDUCATION_WEIGHT = 0.10;

    public function name(): string
    {
        return 'compare_candidates';
    }

    public function description(): Stringable|string
    {
        return 'Compare deux analyses de candidats pour la même offre d\'emploi. Utilisez cet outil pour afficher les différences de scores, forces, lacunes et recommandations entre deux candidats. Les deux candidats doivent être analysés pour la même offre.';
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            $analysis1 = CandidateAnalysis::with('candidate')
                ->whereHas('jobOffer', fn ($q) => $q->where('user_id', auth()->id()))
                ->find($request['analyse_id_1']);

            $analysis2 = CandidateAnalysis::with('candidate')
                ->whereHas('jobOffer', fn ($q) => $q->where('user_id', auth()->id()))
                ->find($request['analyse_id_2']);

            if (! $analysis1) {
                return 'Analyse non trouvée ou accès non autorisé.';
            }

            if (! $analysis2) {
                return 'Analyse non trouvée ou accès non autorisé.';
            }

            if ($analysis1->job_offer_id !== $analysis2->job_offer_id) {
                return 'Erreur : Impossible de comparer des candidats de différentes offres.';
            }

            $analysis1->load('jobOffer');
            $offer = $analysis1->jobOffer;

            $matchingScoreDim1 = $this->computeMatchingScoreDimension($analysis1);
            $matchingScoreDim2 = $this->computeMatchingScoreDimension($analysis2);

            $experienceDim1 = $this->computeExperienceDimension($analysis1, $offer->min_experience_years);
            $experienceDim2 = $this->computeExperienceDimension($analysis2, $offer->min_experience_years);

            $skillsDim1 = $this->computeSkillsDimension($analysis1, $analysis2);
            $skillsDim2 = $this->computeSkillsDimension($analysis2, $analysis1);

            $educationDim1 = $this->computeEducationDimension($analysis1);
            $educationDim2 = $this->computeEducationDimension($analysis2);

            $overallScore1 = ($matchingScoreDim1 * self::MATCHING_SCORE_WEIGHT)
                + ($experienceDim1 * self::EXPERIENCE_WEIGHT)
                + ($skillsDim1 * self::SKILLS_WEIGHT)
                + ($educationDim1 * self::EDUCATION_WEIGHT);

            $overallScore2 = ($matchingScoreDim2 * self::MATCHING_SCORE_WEIGHT)
                + ($experienceDim2 * self::EXPERIENCE_WEIGHT)
                + ($skillsDim2 * self::SKILLS_WEIGHT)
                + ($educationDim2 * self::EDUCATION_WEIGHT);

            $comparisonScore = (int) round(max($overallScore1, $overallScore2));

            $exclusive1 = $this->computeExclusiveSkills($analysis1, $analysis2);
            $exclusive2 = $this->computeExclusiveSkills($analysis2, $analysis1);

            $verdict = $this->buildVerdict($comparisonScore, $overallScore1, $overallScore2, $analysis1, $analysis2);

            return json_encode([
                'candidat_1' => [
                    'nom' => $analysis1->candidate?->name ?? 'Inconnu',
                    'score' => $analysis1->matching_score,
                    'points_forts' => $analysis1->strengths,
                    'lacunes' => $analysis1->gaps,
                    'recommandation' => $analysis1->recommendation?->value,
                ],
                'candidat_2' => [
                    'nom' => $analysis2->candidate?->name ?? 'Inconnu',
                    'score' => $analysis2->matching_score,
                    'points_forts' => $analysis2->strengths,
                    'lacunes' => $analysis2->gaps,
                    'recommandation' => $analysis2->recommendation?->value,
                ],
                'difference_score' => $analysis1->matching_score - $analysis2->matching_score,
                'comparaison_score' => $comparisonScore,
                'verdict' => $verdict['text'],
                'candidat_recommande' => $verdict['recommended'],
                'competences_exclusives_candidat_1' => $exclusive1,
                'competences_exclusives_candidat_2' => $exclusive2,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            return 'Erreur lors de la comparaison des candidats : '.$e->getMessage();
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'analyse_id_1' => $schema->integer()->required(),
            'analyse_id_2' => $schema->integer()->required(),
        ];
    }

    private function computeMatchingScoreDimension(CandidateAnalysis $analysis): float
    {
        return (float) min(100, max(0, $analysis->matching_score));
    }

    private function computeExperienceDimension(CandidateAnalysis $analysis, int $minYears): float
    {
        $years = $analysis->years_experience;

        if ($years === $minYears) {
            return 100;
        }

        if ($years < $minYears) {
            $ratio = $minYears > 0 ? $years / $minYears : 0;

            return (float) round($ratio * 80);
        }

        $over = $years - $minYears;

        return (float) round(max(0, 100 - ($over * 10)));
    }

    private function computeSkillsDimension(CandidateAnalysis $analysis, CandidateAnalysis $other): float
    {
        $skillsA = count($analysis->extracted_skills ?? []);
        $skillsB = count($other->extracted_skills ?? []);
        $max = max($skillsA, $skillsB);

        if ($max === 0) {
            return 0;
        }

        return (float) round(($skillsA / $max) * 100);
    }

    private function computeEducationDimension(CandidateAnalysis $analysis): float
    {
        $level = strtolower(trim($analysis->education_level ?? ''));

        $map = [
            'doctorat' => 100,
            'bac+5' => 80,
            'bac+3' => 60,
            'bac+2' => 40,
            'bac' => 20,
            'bts' => 40,
            'dut' => 40,
            'licence' => 60,
            'master' => 80,
            'master 2' => 80,
            'master 1' => 60,
            'ingénieur' => 80,
            'phd' => 100,
        ];

        foreach ($map as $key => $score) {
            if (str_contains($level, $key)) {
                return (float) $score;
            }
        }

        return 0;
    }

    private function computeExclusiveSkills(CandidateAnalysis $analysis, CandidateAnalysis $other): array
    {
        $skillsA = array_map('strtolower', array_map('trim', $analysis->extracted_skills ?? []));
        $skillsB = array_map('strtolower', array_map('trim', $other->extracted_skills ?? []));

        return array_values(array_diff($skillsA, $skillsB));
    }

    private function buildVerdict(
        int $comparisonScore,
        float $overallScore1,
        float $overallScore2,
        CandidateAnalysis $analysis1,
        CandidateAnalysis $analysis2,
    ): array {
        $name1 = $analysis1->candidate?->name ?? 'Candidat 1';
        $name2 = $analysis2->candidate?->name ?? 'Candidat 2';

        if ($comparisonScore >= 60) {
            $recommended = $overallScore1 >= $overallScore2 ? 'candidat_1' : 'candidat_2';
            $winnerName = $overallScore1 >= $overallScore2 ? $name1 : $name2;

            return [
                'text' => "{$winnerName} est recommandé. Le profil correspond significativement mieux aux critères de l'offre avec un score de comparaison de {$comparisonScore}/100.",
                'recommended' => $recommended,
            ];
        }

        if ($comparisonScore >= 41) {
            $recommended = $overallScore1 >= $overallScore2 ? 'candidat_1' : 'candidat_2';
            $winnerName = $overallScore1 >= $overallScore2 ? $name1 : $name2;
            $otherName = $overallScore1 >= $overallScore2 ? $name2 : $name1;

            return [
                'text' => "{$winnerName} est légèrement mieux positionné que {$otherName}, mais les profils sont proches. Examinez les points forts et lacunes de chaque candidat pour décider.",
                'recommended' => $recommended,
            ];
        }

        return [
            'text' => "{$name1} et {$name2} sont très proches. Aucun candidat ne se démarque clairement. Comparez les détails manuellement pour prendre une décision.",
            'recommended' => null,
        ];
    }
}
