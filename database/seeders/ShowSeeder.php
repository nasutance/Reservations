<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Location;
use App\Models\Show;

class ShowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      //Empty the table first
      DB::statement('SET FOREIGN_KEY_CHECKS=0');
      Show::truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1');

      // Define data
        $data = [
            [
                'slug' => null,
                'title' => 'Ayiti',
                'description' => "Un homme est bloqué à l’aéroport.\n" .
                    'Questionné par les douaniers, il doit alors justifier son identité, ' .
                    'et surtout prouver qu\'il est haïtien – qu\'est-ce qu\'être haïtien ?',
                'poster_url' => 'ayiti.jpg',
                'duration' => 90,
                'created_in' => 2010,
                'location_slug' => 'espace-delvaux-la-venerie',
                'bookable' => true,
            ],
            [
                'slug' => null,
                'title' => 'Cible mouvante',
                'description' => 'Dans ce « thriller d’anticipation », des adultes semblent alimenter ' .
                    'et véhiculer une crainte féroce envers les enfants âgés entre 10 et 12 ans.',
                'poster_url' => 'cible.jpg',
                'duration' => 90,
                'created_in' => 2012,
                'location_slug' => 'dexia-art-center',
                'bookable' => true,
            ],
            [
                'slug' => null,
                'title' => 'Ceci n\'est pas un chanteur belge',
                'description' => "Non peut-être ?!\nEntre Magritte (pour le surréalisme comique) " .
                    'et Maigret (pour le réalisme mélancolique), ce dixième opus semalien propose ' .
                    'quatorze nouvelles chansons mêlées à de petits textes humoristiques et ' .
                    'à quelques fortes images poétiques.',
                'poster_url' => 'claudebelgesaison220.jpg',
                'duration' => 90,
                'created_in' => 2014,
                'location_slug' => null,
                'bookable' => false,
            ],
            [
                'slug' => null,
                'title' => 'Manneke… !',
                'description' => 'A tour de rôle, Pierre se joue de ses oncles, ' .
                    'tantes, grands-parents et surtout de sa mère.',
                'poster_url' => 'wayburn.jpg',
                'duration' => 90,
                'created_in' => 2011,
                'location_slug' => 'la-samaritaine',
                'bookable' => true,
            ],
        ];

        // Prepare the data
        foreach ($data as &$row) {
            // Search the location for a given location's slug
            $location = Location::firstWhere('slug', $row['location_slug']);
            $row['location_id'] = $location->id ?? null;
            unset($row['location_slug']);

            $row['slug'] = Str::slug($row['title'], '-');
        }
        unset($row);

        // Insert data in the table
        DB::table('shows')->insert($data);
    }
}
