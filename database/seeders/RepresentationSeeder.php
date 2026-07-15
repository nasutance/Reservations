<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Representation;
use App\Models\Show;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty the table first
        Schema::disableForeignKeyConstraints();
        Representation::truncate();
        Schema::enableForeignKeyConstraints();

        // Define data — dates relatives à aujourd'hui pour que la démo
        // affiche toujours des représentations à venir
        $data = [
            [
                'location_slug' => 'espace-delvaux-la-venerie',
                'show_slug' => 'ayiti',
                'schedule' => now()->addDays(7)->setTime(13, 30),
            ],
            [
                'location_slug' => 'dexia-art-center',
                'show_slug' => 'ayiti',
                'schedule' => now()->addDays(7)->setTime(20, 30),
            ],
            [
                'location_slug' => null,
                'show_slug' => 'cible-mouvante',
                'schedule' => now()->addDays(14)->setTime(20, 30),
            ],
            [
                'location_slug' => null,
                'show_slug' => 'ceci-nest-pas-un-chanteur-belge',
                'schedule' => now()->addDays(21)->setTime(20, 30),
            ],
            [
                'location_slug' => 'la-samaritaine',
                'show_slug' => 'cible-mouvante',
                'schedule' => now()->addDays(28)->setTime(19, 0),
            ],
        ];

        // Prepare the data
        foreach ($data as &$row) {
            // Search the location for a given location's slug
            $location = ! is_null($row['location_slug'])
                ? Location::firstWhere('slug', $row['location_slug'])
                : null;
            unset($row['location_slug']);

            // Search the show for a given show's slug
            $show = Show::firstWhere('slug', $row['show_slug']);
            unset($row['show_slug']);

            $row['location_id'] = $location->id ?? null;
            $row['show_id'] = $show->id;
        }
        unset($row);

        // Insert data in the table
        DB::table('representations')->insert($data);
    }
}
