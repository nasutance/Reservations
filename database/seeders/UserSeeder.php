<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Vider la table users
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Définir les utilisateurs de test sans rôle
        $users = [
            [
                'login' => 'bob',
                'firstname' => 'Bob',
                'lastname' => 'Sull',
                'email' => 'bob@sull.com',
                'password' => '12345678',
                'remember_token' => Str::random(10),
                'langue' => 'fr',
                'created_at' => '',
            ],
            [
                'login' => 'anna',
                'firstname' => 'Anna',
                'lastname' => 'Lyse',
                'email' => 'anna.lyse@sull.com',
                'password' => '12345678',
                'langue' => 'en',
                'created_at' => '',
            ],
        ];

        foreach ($users as &$user) {
            $user['email_verified_at'] = Carbon::now()->toDateTimeString();
            $user['created_at'] = Carbon::now()->toDateTimeString();
            $user['password'] = Hash::make($user['password']);
            $user['remember_token'] = Str::random(10);
        }

        // Insérer les utilisateurs de test dans la table users
        DB::table('users')->insert($users);

        // Générer des utilisateurs supplémentaires sans rôle via des factories
        User::factory(20)->create();
    }
}
