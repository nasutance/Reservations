<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = ['humour', 'drame', 'jeune public', 'musique', 'impro'];

        foreach ($tags as $label) {
            Tag::firstOrCreate(['tag' => $label]);
        }
    }
}
