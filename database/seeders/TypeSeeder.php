<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Type::truncate();
        Schema::enableForeignKeyConstraints();

        $types =
        [
            ['type' => 'comédien'],
            ['type' => 'scénographe'],
            ['type' => 'auteur'],
        ];
        DB::table('types')->insert($types);
    }
}
