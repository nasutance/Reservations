<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\Show;
use App\Models\User;
use App\Models\Role;
use App\Models\Location;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ShowControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdmin(): User
    {
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate(['role' => 'admin']);
        $user->roles()->attach($adminRole->id);
        return $user;
    }

    public function test_user_can_get_all_shows()
{

    $user = User::factory()->create();
    Sanctum::actingAs($user); // Même correction ici
    DB::table('representation_reservation')->delete();
    DB::table('representations')->delete();
    DB::table('artist_type_show')->delete();
    DB::table('price_show')->delete();
    DB::table('reviews')->delete();
    Show::query()->delete();
    Show::factory()->count(3)->create();

    $response = $this->getJson('/api/shows');

    $response->assertStatus(200)
             ->assertJsonCount(3);
}


    public function test_admin_can_create_a_show()
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $location = Location::factory()->create();

        $response = $this->postJson('/api/shows', [
            'title' => 'New Show',
            'slug' => 'new-show',
            'description' => 'Description du spectacle',
            'poster_url' => 'https://example.com/poster.jpg',
            'duration' => 120,
            'created_in' => 2024,
            'location_id' => $location->id,
            'bookable' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['title' => 'New Show']);
    }

    public function test_guest_cannot_create_a_show()
    {
        $location = Location::factory()->create();

        $response = $this->postJson('/api/shows', [
            'title' => 'Unauthorized Show',
            'slug' => 'unauthorized-show',
            'description' => 'Description',
            'poster_url' => 'https://example.com/poster.jpg',
            'duration' => 120,
            'created_in' => 2024,
            'location_id' => $location->id,
            'bookable' => true,
        ]);

        $response->assertStatus(401);
    }
}
