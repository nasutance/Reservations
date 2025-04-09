<?php

// database/factories/PriceFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 100), 
            'description' => $this->faker->sentence(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional()->date(),
        ];
    }
}
