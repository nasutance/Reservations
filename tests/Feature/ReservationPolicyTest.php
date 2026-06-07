<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Price;
use App\Models\Representation;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservationPolicyTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::firstOrCreate(['role' => $role])->id);
        return $user;
    }

    // -------------------------------------------------------------------------
    // viewAny : admin voit toutes les réservations, membre uniquement les siennes
    // -------------------------------------------------------------------------

    public function test_admin_can_list_all_reservations()
    {
        $admin  = $this->createUserWithRole('admin');
        $member = $this->createUserWithRole('member');
        Reservation::factory()->count(2)->create(['user_id' => $member->id]);

        Sanctum::actingAs($admin);
        $this->getJson('/api/reservations')->assertStatus(200)->assertJsonCount(2);
    }

    public function test_member_only_sees_own_reservations()
    {
        $member = $this->createUserWithRole('member');
        $other  = $this->createUserWithRole('member');
        Reservation::factory()->create(['user_id' => $member->id]);
        Reservation::factory()->create(['user_id' => $other->id]);

        Sanctum::actingAs($member);
        $this->getJson('/api/reservations')->assertStatus(200)->assertJsonCount(1);
    }

    // -------------------------------------------------------------------------
    // view : propriétaire ou admin — pas les autres
    // -------------------------------------------------------------------------

    public function test_member_can_view_own_reservation()
    {
        $member      = $this->createUserWithRole('member');
        $reservation = Reservation::factory()->create(['user_id' => $member->id]);

        Sanctum::actingAs($member);
        $this->getJson("/api/reservations/{$reservation->id}")->assertStatus(200);
    }

    public function test_member_cannot_view_another_users_reservation()
    {
        $member      = $this->createUserWithRole('member');
        $other       = $this->createUserWithRole('member');
        $reservation = Reservation::factory()->create(['user_id' => $other->id]);

        Sanctum::actingAs($member);
        $this->getJson("/api/reservations/{$reservation->id}")->assertStatus(403);
    }

    public function test_admin_can_view_any_reservation()
    {
        $admin       = $this->createUserWithRole('admin');
        $member      = $this->createUserWithRole('member');
        $reservation = Reservation::factory()->create(['user_id' => $member->id]);

        Sanctum::actingAs($admin);
        $this->getJson("/api/reservations/{$reservation->id}")->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // create : member/affiliate → oui  |  admin seul → non
    // -------------------------------------------------------------------------

    public function test_member_can_create_reservation()
    {
        $member         = $this->createUserWithRole('member');
        $location       = Location::factory()->create();
        $show           = Show::factory()->create(['location_id' => $location->id]);
        $representation = Representation::factory()->create([
            'show_id'     => $show->id,
            'location_id' => $location->id,
        ]);
        $price = Price::factory()->create();
        $show->prices()->attach($price->id);

        Sanctum::actingAs($member);
        $this->postJson('/api/reservations', [
            'representation_id' => $representation->id,
            'price_id'          => $price->id,
            'quantity'          => 2,
        ])->assertStatus(201);
    }

    public function test_affiliate_can_create_reservation()
    {
        $affiliate      = $this->createUserWithRole('affiliate');
        $location       = Location::factory()->create();
        $show           = Show::factory()->create(['location_id' => $location->id]);
        $representation = Representation::factory()->create([
            'show_id'     => $show->id,
            'location_id' => $location->id,
        ]);
        $price = Price::factory()->create();
        $show->prices()->attach($price->id);

        Sanctum::actingAs($affiliate);
        $this->postJson('/api/reservations', [
            'representation_id' => $representation->id,
            'price_id'          => $price->id,
            'quantity'          => 1,
        ])->assertStatus(201);
    }

    public function test_admin_without_member_role_cannot_create_reservation()
    {
        $admin          = $this->createUserWithRole('admin');
        $representation = Representation::factory()->create();
        $price          = Price::factory()->create();

        Sanctum::actingAs($admin);
        $this->postJson('/api/reservations', [
            'representation_id' => $representation->id,
            'price_id'          => $price->id,
            'quantity'          => 1,
        ])->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // delete : propriétaire ou admin — pas les autres
    // -------------------------------------------------------------------------

    public function test_owner_can_cancel_own_reservation()
    {
        $member      = $this->createUserWithRole('member');
        $reservation = Reservation::factory()->create(['user_id' => $member->id]);

        Sanctum::actingAs($member);
        $this->deleteJson("/api/reservations/{$reservation->id}")->assertStatus(200);
    }

    public function test_member_cannot_cancel_another_users_reservation()
    {
        $member      = $this->createUserWithRole('member');
        $other       = $this->createUserWithRole('member');
        $reservation = Reservation::factory()->create(['user_id' => $other->id]);

        Sanctum::actingAs($member);
        $this->deleteJson("/api/reservations/{$reservation->id}")->assertStatus(403);
    }

    public function test_admin_can_cancel_any_reservation()
    {
        $admin       = $this->createUserWithRole('admin');
        $member      = $this->createUserWithRole('member');
        $reservation = Reservation::factory()->create(['user_id' => $member->id]);

        Sanctum::actingAs($admin);
        $this->deleteJson("/api/reservations/{$reservation->id}")->assertStatus(200);
    }
}
