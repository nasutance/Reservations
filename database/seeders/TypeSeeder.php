<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Type;

class TypeSeeder extends Seeder
{
  public function run(): void
  {
    Type::truncate();
    $types =
    [
      ['type'=>'comédien'],
      ['type'=>'scénographe'],
      ['type'=>'auteur'],
    ];
    DB::table('types')->insert($types);
  }
}
