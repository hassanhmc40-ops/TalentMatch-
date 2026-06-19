<?php

namespace App\Ai\Tools;

use App\Models\CandidateAnalysis;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CompareCandidates implements Tool
{
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
}
