<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Representation;
use App\Models\Location;
use App\Models\Show;

class RepresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty the table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Representation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define data
        $data = [
            [
                'location_slug' => 'espace-delvaux-la-venerie',
                'show_slug' => 'ayiti',
                'schedule' => '2012-10-12 13:30',
            ],
            [
                'location_slug' => 'dexia-art-center',
                'show_slug' => 'ayiti',
                'schedule' => '2012-10-12 20:30',
            ],
            [
                'location_slug' => null,
                'show_slug' => 'cible-mouvante',
                'schedule' => '2012-10-02 20:30',
            ],
            [
                'location_slug' => null,
                'show_slug' => 'ceci-nest-pas-un-chanteur-belge',
                'schedule' => '2012-10-16 20:30',
            ],
        ];

        // Prepare the data
        foreach ($data as &$row) {
            // Search the location for a given location's slug
            $location = !is_null($row['location_slug'])
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
