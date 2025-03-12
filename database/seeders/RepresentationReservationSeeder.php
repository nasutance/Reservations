<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Representation;
use App\Models\Reservation;
use App\Models\Price;

class RepresentationReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table avant de la remplir
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('representation_reservation')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $data = [];

        // Vérifier les données nécessaires
        $reservations = Reservation::all();
        $representations = Representation::where('schedule', '2012-10-12 20:30')->get();
        $prices = Price::all();

        if ($reservations->isEmpty()) {
            $this->command->warn('Aucune réservation disponible. Seeder annulé.');
            return;
        }

        if ($representations->isEmpty()) {
            $this->command->warn('Aucune représentation disponible avec l\'horaire spécifié. Seeder annulé.');
            return;
        }

        if ($prices->isEmpty()) {
            $this->command->warn('Aucun prix disponible. Seeder annulé.');
            return;
        }

        // Ajouter des données
        foreach ($representations as $repres) {
            foreach ($reservations as $res) {
                $data[] = [
                    'representation_id' => $repres->id,
                    'reservation_id' => $res->id,
                    'price_id' => $prices->random()->id,
                    'quantity' => rand(1, 5),
                ];
            }
        }

        // Insérer dans la base de données
        DB::table('representation_reservation')->insert($data);

        $this->command->info('Données insérées dans representation_reservation.');
    }
}
