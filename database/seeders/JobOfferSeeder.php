<?php

namespace Database\Seeders;

use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobOfferSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'rh@talentmatch.ma'],
            [
                'name' => 'Responsable RH',
                'password' => bcrypt('password'),
            ]
        );

        JobOffer::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
    }
}
