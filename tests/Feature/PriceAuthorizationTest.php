<?php

namespace Tests\Feature;

use App\Models\Price;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PriceAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::firstOrCreate(['role' => $role])->id);

        return $user;
    }

    private function validPayload(string $type): array
    {
        return [
            'type' => $type,
            'description' => 'Tarif de test',
            'price' => 8.50,
            'start_date' => '2026-01-01',
        ];
    }

    public function test_guest_cannot_create_a_price(): void
    {
        $response = $this->post('/price', $this->validPayload('Tarif pirate'));

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('prices', ['type' => 'Tarif pirate']);
    }

    public function test_member_cannot_create_a_price(): void
    {
        $this->actingAs($this->createUserWithRole('member'));

        $this->post('/price', $this->validPayload('Tarif membre'))->assertStatus(403);
        $this->assertDatabaseMissing('prices', ['type' => 'Tarif membre']);
    }

    public function test_admin_can_create_a_price(): void
    {
        $this->actingAs($this->createUserWithRole('admin'));

        $this->post('/price', $this->validPayload('Tarif senior'));

        $this->assertDatabaseHas('prices', ['type' => 'Tarif senior']);
    }

    public function test_member_cannot_update_or_delete_a_price(): void
    {
        $price = Price::factory()->create();
        $this->actingAs($this->createUserWithRole('member'));

        $this->put("/price/{$price->id}", $this->validPayload('Modifié'))->assertStatus(403);

        $this->delete("/price/{$price->id}")->assertStatus(403);
        $this->assertDatabaseHas('prices', ['id' => $price->id]);
    }
}
