<?php

namespace Tests\Feature;

use App\Models\Price;
use App\Models\Representation;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReservationLineTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::firstOrCreate(['role' => $role])->id);

        return $user;
    }

    private function createReservationWithLine(User $owner): array
    {
        $reservation = Reservation::factory()->create(['user_id' => $owner->id]);
        $representation = Representation::factory()->create();
        $price = Price::factory()->create();

        $reservation->representations()->attach($representation->id, [
            'price_id' => $price->id,
            'quantity' => 2,
        ]);

        return [$reservation, $representation, $price];
    }

    public function test_owner_can_delete_a_line_of_his_reservation(): void
    {
        $owner = $this->createUserWithRole('member');
        [$reservation, $representation, $price] = $this->createReservationWithLine($owner);

        $this->actingAs($owner);

        $this->delete("/reservation/{$reservation->id}/line/{$representation->id}/{$price->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('representation_reservation', [
            'reservation_id' => $reservation->id,
            'representation_id' => $representation->id,
            'price_id' => $price->id,
        ]);
    }

    public function test_user_cannot_delete_a_line_of_someone_elses_reservation(): void
    {
        $owner = $this->createUserWithRole('member');
        $intruder = $this->createUserWithRole('member');
        [$reservation, $representation, $price] = $this->createReservationWithLine($owner);

        $this->actingAs($intruder);

        $this->delete("/reservation/{$reservation->id}/line/{$representation->id}/{$price->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('representation_reservation', [
            'reservation_id' => $reservation->id,
            'representation_id' => $representation->id,
            'price_id' => $price->id,
        ]);
    }
}
