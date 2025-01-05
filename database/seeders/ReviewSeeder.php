<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Models\User; 
use App\Models\Show;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vide la table avant d'insérer de nouvelles données
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Review::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Données fictives pour les avis
        $reviews = [
            [
                'user_id' => User::first()->id, // Récupère l'ID du premier utilisateur
                'show_id' => Show::first()->id, // Récupère l'ID du premier spectacle
                'review' => 'Un spectacle fantastique avec une performance incroyable.',
                'stars' => 5,
                'validated' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => User::find(2)?->id ?? User::first()->id, // ID utilisateur 2 ou premier utilisateur
                'show_id' => Show::find(2)?->id ?? Show::first()->id, // ID spectacle 2 ou premier spectacle
                'review' => 'J\'ai trouvé le scénario un peu faible, mais les acteurs étaient superbes.',
                'stars' => 3,
                'validated' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => User::find(3)?->id ?? User::first()->id,
                'show_id' => Show::find(1)?->id ?? Show::first()->id,
                'review' => 'Un chef-d\'œuvre qui restera gravé dans ma mémoire.',
                'stars' => 4,
                'validated' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insère les données dans la table reviews
        Review::insert($reviews);
    }
}
