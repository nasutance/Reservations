<?php

namespace Database\Factories;

use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocalityFactory extends Factory
{
    protected $model = Locality::class;

    public function definition(): array
    {
        return [
            'postal_code' => $this->faker->unique()->numberBetween(1000, 9999),
            'locality' => $this->faker->city(),
        ];
    }
}
