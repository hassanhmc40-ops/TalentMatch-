<?php

namespace Database\Factories;

use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobOffer>
 */
class JobOfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Développeur PHP Laravel',
                'Ingénieur DevOps',
                'Chef de projet digital',
                'Data Analyst',
                'Designer UX/UI',
            ]),
            'description' => fake()->realTextBetween(100, 300),
            'required_skills' => fake()->randomElements([
                'PHP', 'Laravel', 'MySQL', 'Git', 'Docker',
                'JavaScript', 'Vue.js', 'React', 'Node.js',
                'Python', 'AWS', 'Linux', 'Redis', 'CSS',
            ], fake()->numberBetween(2, 5)),
            'min_experience_years' => fake()->numberBetween(0, 10),
        ];
    }
}
