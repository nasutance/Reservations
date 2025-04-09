<?php

namespace Database\Factories;

use App\Models\Representation;
use App\Models\Show;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepresentationFactory extends Factory
{
    protected $model = Representation::class;

    public function definition()
    {
        return [
            'show_id' => Show::factory(), // 🔹 Crée un show associé
            'schedule' => $this->faker->dateTimeBetween('now', '+1 year'),
            'location_id' => Location::factory(), // 🔹 Crée une location associée
        ];
    }
}
