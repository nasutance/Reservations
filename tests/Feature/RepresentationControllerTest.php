<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Location;
use App\Models\Show;
use App\Models\Representation;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RepresentationControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdmin(): User
    {
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate(['role' => 'admin']);
        $user->roles()->attach($adminRole->id);
        return $user;
    }

    public function test_user_can_get_all_representations()
  {
      $user = User::factory()->create();
      Sanctum::actingAs($user);

      DB::table('representation_reservation')->delete();
Representation::query()->delete();

      Representation::factory()->count(5)->create();

      $response = $this->getJson('/api/representations');

      $response->assertStatus(200)
               ->assertJsonCount(5);
  }

    public function test_admin_can_create_a_representation()
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $location = Location::factory()->create();
        $show = Show::factory()->create(['location_id' => $location->id]);

        $response = $this->postJson('/api/representations', [
            'show_id' => $show->id,
            'schedule' => '2024-05-01 20:00:00',
            'location_id' => $location->id,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['schedule' => '2024-05-01 20:00:00']);
    }

    public function test_guest_cannot_create_a_representation()
    {
        $location = Location::factory()->create();
        $show = Show::factory()->create(['location_id' => $location->id]);

        $response = $this->postJson('/api/representations', [
            'show_id' => $show->id,
            'schedule' => '2024-05-01 20:00:00',
            'location_id' => $location->id,
        ]);

        $response->assertStatus(401);
    }
}
