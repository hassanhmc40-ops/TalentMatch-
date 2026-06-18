<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class CvAnalysisAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return 'You are a CV analysis assistant. Extract structured information from the candidate CV text and match it against the given job offer requirements. Return the analysis in the specified JSON format.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'competences_extraites' => $schema->array()
                ->items($schema->string())
                ->required(),
            'annees_experience' => $schema->integer()->min(0)->required(),
            'niveau_etudes' => $schema->string()->required(),
            'langues' => $schema->array()
                ->items($schema->string())
                ->required(),
            'matching_score' => $schema->integer()->min(0)->max(100)->required(),
            'points_forts' => $schema->array()
                ->items($schema->string())
                ->required(),
            'lacunes' => $schema->array()
                ->items($schema->string())
                ->required(),
            'competences_manquantes' => $schema->array()
                ->items($schema->string())
                ->required(),
            'recommandation' => $schema->string()->enum(['convoquer', 'attente', 'rejeter'])->required(),
            'justification' => $schema->string()->required(),
        ];
    }
}
