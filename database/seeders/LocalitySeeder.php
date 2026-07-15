<?php

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Empty the table first
        Schema::disableForeignKeyConstraints();
        Locality::truncate();
        Schema::enableForeignKeyConstraints();

        //Define data
        $data =
        [
            [
                'postal_code' => '1000',
                'locality' => 'Bruxelles',
            ],
            [
                'postal_code' => '1040',
                'locality' => 'Etterbeek',
            ],
            [
                'postal_code' => '1050',
                'locality' => 'Ixelles',
            ],
            [
                'postal_code' => '1170',
                'locality' => 'Watermael-Boitsfort',
            ],
        ];

        //Insert data in the table
        DB::table('localities')->insert($data);
    }
}
