<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Empty the table first
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        //Define data
        $data =
        [
            [
                'role' => 'admin',
            ],
            [
                'role' => 'member',
            ],
            [
                'role' => 'affiliate',
            ],
            [
                'role' => 'press',
            ],
        ];

        //Insert data in the table
        DB::table('roles')->insert($data);
    }
}
