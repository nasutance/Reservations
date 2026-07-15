<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArtistTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty the table first
        Schema::disableForeignKeyConstraints();
        DB::table('artist_type')->truncate();
        Schema::enableForeignKeyConstraints();

        // Define data
        $data = [
            ['artist_firstname' => 'Daniel', 'artist_lastname' => 'Marcelin', 'type' => 'auteur'],
            ['artist_firstname' => 'Philippe', 'artist_lastname' => 'Laurent', 'type' => 'auteur'],
            ['artist_firstname' => 'Daniel', 'artist_lastname' => 'Marcelin', 'type' => 'scénographe'],
            ['artist_firstname' => 'Philippe', 'artist_lastname' => 'Laurent', 'type' => 'scénographe'],
            ['artist_firstname' => 'Daniel', 'artist_lastname' => 'Marcelin', 'type' => 'comédien'],
            ['artist_firstname' => 'Marius', 'artist_lastname' => 'Von Mayenburg', 'type' => 'auteur'],
            ['artist_firstname' => 'Olivier', 'artist_lastname' => 'Boudon', 'type' => 'scénographe'],
            ['artist_firstname' => 'Anne Marie', 'artist_lastname' => 'Loop', 'type' => 'comédien'],
            ['artist_firstname' => 'Pietro', 'artist_lastname' => 'Varasso', 'type' => 'comédien'],
            ['artist_firstname' => 'Laurent', 'artist_lastname' => 'Caron', 'type' => 'comédien'],
            ['artist_firstname' => 'Élena', 'artist_lastname' => 'Perez', 'type' => 'comédien'],
            ['artist_firstname' => 'Guillaume', 'artist_lastname' => 'Alexandre', 'type' => 'comédien'],
            ['artist_firstname' => 'Claude', 'artist_lastname' => 'Semal', 'type' => 'auteur'],
            ['artist_firstname' => 'Laurence', 'artist_lastname' => 'Warin', 'type' => 'scénographe'],
            ['artist_firstname' => 'Claude', 'artist_lastname' => 'Semal', 'type' => 'comédien'],
            ['artist_firstname' => 'Pierre', 'artist_lastname' => 'Wayburn', 'type' => 'auteur'],
            ['artist_firstname' => 'Gwendoline', 'artist_lastname' => 'Gauthier', 'type' => 'auteur'],
            ['artist_firstname' => 'Philippe', 'artist_lastname' => 'Laurent', 'type' => 'scénographe'],
            ['artist_firstname' => 'Pierre', 'artist_lastname' => 'Wayburn', 'type' => 'comédien'],
            ['artist_firstname' => 'Gwendoline', 'artist_lastname' => 'Gauthier', 'type' => 'comédien'],
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

            unset($row['artist_firstname']);
            unset($row['artist_lastname']);
            unset($row['type']);

            $row['artist_id'] = $artist->id;
            $row['type_id'] = $type->id;
        }
        unset($row);

        // Insert data in the table
        DB::table('artist_type')->insert($data);
    }
}
