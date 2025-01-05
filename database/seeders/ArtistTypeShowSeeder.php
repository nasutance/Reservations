<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Artist;
use App\Models\Type;
use App\Models\ArtistType;
use App\Models\Show;

class ArtistTypeShowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty the table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('artist_type_show')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define data
        $data = [
            [
                'artist_firstname' => 'Daniel',
                'artist_lastname' => 'Marcelin',
                'type' => 'auteur',
                'show_slug' => 'ayiti',
            ],
            [
                'artist_firstname' => 'Philippe',
                'artist_lastname' => 'Laurent',
                'type' => 'auteur',
                'show_slug' => 'ayiti',
            ],
            [
                'artist_firstname' => 'Daniel',
                'artist_lastname' => 'Marcelin',
                'type' => 'scénographe',
                'show_slug' => 'ayiti',
            ],
            // Add the rest of the data here following the same structure
        ];

        // Prepare the data
        foreach ($data as &$row) {
            // Search the artist for a given artist's firstname and lastname
            $artist = Artist::where([
                ['firstname', '=', $row['artist_firstname']],
                ['lastname', '=', $row['artist_lastname']],
            ])->first();

            // Search the type for a given type
            $type = Type::firstWhere('type', $row['type']);

            // Search the artistType for the artist and type found
            $artistType = ArtistType::where([
                ['artist_id', '=', $artist->id],
                ['type_id', '=', $type->id],
            ])->first();

            // Search the show for a given show's slug
            $show = Show::firstWhere('slug', $row['show_slug']);

            unset($row['artist_firstname']);
            unset($row['artist_lastname']);
            unset($row['type']);
            unset($row['show_slug']);

            $row['artist_type_id'] = $artistType->id;
            $row['show_id'] = $show->id;
        }
        unset($row);

        // Insert data in the table
        DB::table('artist_type_show')->insert($data);
    }
}
