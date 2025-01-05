<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::statement('SET FOREIGN_KEY_CHECKS=0');
      DB::table('role_user')->truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1');
        // Récupère tous les rôles
        $roles = Role::all();

        // Assigne des rôles aléatoires aux utilisateurs
        User::all()->each(function ($user) use ($roles) {
            $user->roles()->attach(
                $roles->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
