<?php

namespace Database\Factories;

use App\Enums\Recommendation;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateAnalysis>
 */
class CandidateAnalysisFactory extends Factory
{
    protected $model = CandidateAnalysis::class;

    public function definition(): array
    {
        return [
            'job_offer_id' => JobOffer::factory(),
            'candidate_id' => Candidate::factory(),
            'extracted_skills' => fake()->randomElements([
                'PHP', 'Laravel', 'MySQL', 'JavaScript', 'Python',
            ], fake()->numberBetween(2, 4)),
            'years_experience' => fake()->numberBetween(0, 15),
            'education_level' => fake()->randomElement([
                'Bac', 'Bac+2', 'Bac+3', 'Bac+5', 'Doctorat',
            ]),
            'languages' => fake()->randomElements([
                'Français', 'Anglais', 'Arabe', 'Espagnol',
            ], fake()->numberBetween(1, 3)),
            'matching_score' => fake()->numberBetween(0, 100),
            'strengths' => fake()->randomElements([
                'Expérience en gestion de projet',
                'Maîtrise des outils DevOps',
                'Bonnes compétences en communication',
            ], fake()->numberBetween(1, 3)),
            'gaps' => fake()->randomElements([
                'Manque d\'expérience en management',
                'Absence de certification',
            ], fake()->numberBetween(1, 2)),
            'missing_skills' => fake()->randomElements([
                'Docker', 'Kubernetes', 'React', 'Vue.js',
            ], fake()->numberBetween(1, 2)),
            'recommendation' => fake()->randomElement([
                Recommendation::Convoquer,
                Recommendation::Attente,
                Recommendation::Rejeter,
            ]),
            'justification' => fake()->realTextBetween(50, 150),
            'status' => 'completed',
        ];
    }
}
