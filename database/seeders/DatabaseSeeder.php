<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profil;
use App\Models\Competence;
use App\Models\Offre;
use App\Models\Candidature;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 2 admins
        User::factory(2)->admin()->create();

        // 10 competences
        $competences = Competence::factory(10)->create();

        // 5 recruteurs with 2 to 3 offres each
        User::factory(5)->recruteur()->create()->each(function ($recruteur) {
            Offre::factory(rand(2, 3))->create([
                'user_id' => $recruteur->id,
            ]);
        });

        // 10 candidats with profil and competences
        User::factory(10)->candidat()->create()->each(function ($candidat) use ($competences) {
            $profil = Profil::factory()->create([
                'user_id' => $candidat->id,
            ]);

            $randomCompetences = $competences->random(rand(2, 4));
            foreach ($randomCompetences as $competence) {
                $profil->competences()->attach($competence->id, [
                    'niveau' => fake()->randomElement(['débutant', 'intermédiaire', 'expert']),
                ]);
            }
        });
    }
}