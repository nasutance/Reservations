<?php

namespace Database\Factories;

use App\Models\Show;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShowFactory extends Factory
{
    protected $model = Show::class;

    public function definition()
    {
        return [
          'slug' => substr($this->faker->slug(), 0, 60),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'poster_url' => $this->faker->imageUrl(),
            'duration' => $this->faker->numberBetween(60, 180),
            'created_in' => $this->faker->year,
            'location_id' => Location::factory(), // 🔹 Crée une location associée
            'bookable' => $this->faker->boolean,
        ];
    }
}
