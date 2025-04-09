<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Location;
use App\Models\Show;
use App\Models\Representation;
use App\Models\Reservation;
use App\Models\Price;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function createUser(): User
    {
        return User::factory()->create();
    }

    public function test_user_can_make_a_reservation()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $location = Location::factory()->create();
        $show = Show::factory()->create(['location_id' => $location->id]);
        $representation = Representation::factory()->create([
            'show_id' => $show->id,
            'location_id' => $location->id,
        ]);
        $price = Price::factory()->create();
        $show->prices()->attach($price->id);

        $response = $this->postJson('/api/reservations', [
            'representation_id' => $representation->id,
            'price_id' => $price->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['status' => 'en attente']);
    }

    public function test_guest_cannot_make_a_reservation()
    {
        $representation = Representation::factory()->create();
        $price = Price::factory()->create();

        $response = $this->postJson('/api/reservations', [
            'representation_id' => $representation->id,
            'price_id' => $price->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_get_their_reservations()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        Reservation::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/reservations');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }
}
