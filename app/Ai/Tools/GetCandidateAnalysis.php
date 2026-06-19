<?php

namespace App\Ai\Tools;

use App\Models\CandidateAnalysis;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetCandidateAnalysis implements Tool
{
    public function description(): Stringable|string
    {
        return 'Récupère l\'analyse complète d\'un candidat depuis la base de données par son ID. Utilisez cet outil pour obtenir les compétences extraites, l\'expérience, la formation, les langues, le score de correspondance, les forces, les lacunes, les compétences manquantes, la recommandation et la justification.';
    }

    public function handle(Request $request): Stringable|string
    {
        $analysis = CandidateAnalysis::with('candidate', 'jobOffer')
            ->where('candidate_id', $request['candidat_id'])
            ->whereHas('jobOffer', fn ($q) => $q->where('user_id', auth()->id()))
            ->first();

        if (! $analysis) {
            return 'Analyse non trouvée ou accès non autorisé.';
        }

        return json_encode([
            'candidat' => $analysis->candidate?->name ?? 'Inconnu',
            'offre' => $analysis->jobOffer?->title ?? 'Inconnue',
            'competences_extraites' => $analysis->extracted_skills,
            'annees_experience' => $analysis->years_experience,
            'niveau_etudes' => $analysis->education_level,
            'langues' => $analysis->languages,
            'matching_score' => $analysis->matching_score,
            'points_forts' => $analysis->strengths,
            'lacunes' => $analysis->gaps,
            'competences_manquantes' => $analysis->missing_skills,
            'recommandation' => $analysis->recommendation?->value,
            'justification' => $analysis->justification,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'candidat_id' => $schema->integer()->required(),
        ];
    }
}
