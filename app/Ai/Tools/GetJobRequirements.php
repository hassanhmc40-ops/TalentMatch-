<?php

namespace App\Ai\Tools;

use App\Models\JobOffer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetJobRequirements implements Tool
{
    public function description(): Stringable|string
    {
        return 'Retrieves job offer criteria from the database by offer ID. Use this to get the title, description, required skills, and minimum experience requirements for any job offer.';
    }

    public function handle(Request $request): Stringable|string
    {
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
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'offre_id' => $schema->integer()->required(),
        ];
    }
}
