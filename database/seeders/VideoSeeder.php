<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Video::create([
            'title' => 'Teaser Manneke',
            'video_url' => 'https://www.youtube.com/embed/cyusle1N3zE?si=R4qt4jjp9YYAi684',
            'show_id' => 4
        ]);
        
    }
}
