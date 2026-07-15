<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('role_user')->truncate();
        Schema::enableForeignKeyConstraints();
        // Récupère tous les rôles
        $roles = Role::all();

        // Comptes de démonstration : rôles fixes pour pouvoir tester chaque profil
        $demoRoles = [
            'bob@sull.com' => ['admin', 'member'],
            'anna.lyse@sull.com' => ['member'],
        ];

        User::all()->each(function ($user) use ($roles, $demoRoles) {
            if (isset($demoRoles[$user->email])) {
                $user->roles()->attach(
                    $roles->whereIn('role', $demoRoles[$user->email])->pluck('id')->toArray()
                );

                return;
            }

            // Rôles aléatoires pour les utilisateurs générés par factory
            $user->roles()->attach(
                $roles->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
