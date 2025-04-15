<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

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
