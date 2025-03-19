<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  protected function setUp(): void
  {
      parent::setUp();

      // Réinitialiser le Faker pour éviter les doublons
      fake()->unique(reset: true);
  }

}
