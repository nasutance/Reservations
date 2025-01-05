<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\User;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty the table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Reservation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define data
        $data = [
            [
                'user_firstname' => 'Bob',
                'user_lastname' => 'Sull',
                'status' => 'En attente',
            ],
            [
                'user_firstname' => 'Bob',
                'user_lastname' => 'Sull',
                'status' => 'Annulée',
            ],
            [
                'user_firstname' => 'Bob',
                'user_lastname' => 'Sull',
                'status' => 'Payée',
            ],
            [
                'user_firstname' => 'Anna',
                'user_lastname' => 'Lyse',
                'status' => 'Payée',
            ],
        ];

        // Insert data in the table
        foreach ($data as &$row) {
            // Recherche du membre correspondant
            $user = User::firstWhere([
                'firstname' => $row['user_firstname'],
                'lastname' => $row['user_lastname'],
            ]);

            unset($row['user_firstname']);
            unset($row['user_lastname']);

            $row['user_id'] = $user->id; // Référence à la table users
        }

        DB::table('reservations')->insert($data);
    }
}
