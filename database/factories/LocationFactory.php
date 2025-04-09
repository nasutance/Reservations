<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    // LocationFactory.php
    public function definition(): array
  {
      return [
          'slug' => substr($this->faker->slug(), 0, 50),
          'designation' => $this->faker->company(),
          'address' => $this->faker->streetAddress(),
          'locality_postal_code' => Locality::factory()->create()->postal_code,
          'website' => $this->faker->url(),
          'phone' => $this->faker->phoneNumber(),
      ];
  }

}
