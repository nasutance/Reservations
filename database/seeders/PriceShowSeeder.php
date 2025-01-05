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
        // Désactiver les vérifications des clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('price_show')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Récupérer les shows et les prix valides
        $shows = Show::all();
        $prices = Price::where('end_date', '>=', now()->format('Y-m-d'))
                       ->orWhereNull('end_date')
                       ->get();

        // Vérifier si des données sont disponibles
        if ($shows->isEmpty()) {
            $this->command->warn('Aucun show disponible dans la base. Seeder annulé.');
            return;
        }

        if ($prices->isEmpty()) {
            $this->command->warn('Aucun prix valide disponible dans la base. Seeder annulé.');
            return;
        }

        // Préparer les données à insérer
        $data = [];
        foreach ($shows as $show) {
            foreach ($prices as $price) {
                $data[] = [
                    'show_id' => $show->id,
                    'price_id' => $price->id,
                ];
            }
        }

        // Insérer les données dans la table
        DB::table('price_show')->insert($data);

        $this->command->info('Données insérées dans la table price_show.');
    }
}
