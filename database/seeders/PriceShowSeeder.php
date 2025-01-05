<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Price;
use App\Models\Show;

class PriceShowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('price_show')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define data
        $data = [];

        $shows = Show::all();
        $prices = Price::whereNull('end_date')->get(); // end_date null only

        // Add each valid price to each show
        foreach ($shows as $show) {
            foreach ($prices as $price) {
                $data[] = [
                    'show_id' => $show->id,
                    'price_id' => $price->id,
                ];
            }
        }

        // Insert data in the table
        DB::table('price_show')->insert($data);
    }
}
