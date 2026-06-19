<?php

namespace App\Ai\Tools;

use App\Models\JobOffer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetJobRequirements implements Tool
{
    public function name(): string
    {
        return 'get_job_requirements';
    }

    public function description(): Stringable|string
    {
        return 'Récupère les critères d\'une offre d\'emploi depuis la base de données par son ID. Utilisez cet outil pour obtenir le titre, la description, les compétences requises et l\'expérience minimale requise.';
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            $offer = JobOffer::query()
                ->where('id', $request['offre_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (! $offer) {
                return 'Offre non trouvée ou accès non autorisé.';
            }

            return json_encode([
                'titre' => $offer->title,
                'description' => $offer->description,
                'competences_requises' => $offer->required_skills,
                'annees_experience_minimum' => $offer->min_experience_years,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            return 'Erreur lors de la récupération des critères de l\'offre : '.$e->getMessage();
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'offre_id' => $schema->integer()->required(),
        ];
    }
}
