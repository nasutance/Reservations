<?php

namespace Database\Seeders;

use App\Models\Price;
use App\Models\Representation;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepresentationReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table avant de la remplir
        Schema::disableForeignKeyConstraints();
        DB::table('representation_reservation')->truncate();
        Schema::enableForeignKeyConstraints();

        $data = [];

        // Vérifier les données nécessaires
        $reservations = Reservation::all();
        // Les deux prochaines représentations programmées (dates relatives au seed)
        $representations = Representation::orderBy('schedule')->take(2)->get();
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
