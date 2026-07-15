<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::firstOrCreate(['role' => $role])->id);

        return $user;
    }

    public function test_member_cannot_grant_himself_the_admin_role(): void
    {
        $member = $this->createUserWithRole('member');
        $adminRole = Role::firstOrCreate(['role' => 'admin']);

        $this->actingAs($member);

        $this->put("/users/{$member->id}", [
            'firstname' => $member->firstname,
            'lastname' => $member->lastname,
            'email' => $member->email,
            'roles' => [$adminRole->id],
        ]);

        $this->assertFalse($member->fresh()->load('roles')->hasRole('admin'));
    }

    public function test_member_cannot_update_another_user(): void
    {
        $member = $this->createUserWithRole('member');
        $victim = $this->createUserWithRole('member');

        $this->actingAs($member);

        $this->put("/users/{$victim->id}", [
            'firstname' => 'Pwned',
            'lastname' => $victim->lastname,
            'email' => $victim->email,
        ])->assertStatus(403);
    }

    public function test_member_cannot_delete_another_user(): void
    {
        $member = $this->createUserWithRole('member');
        $victim = $this->createUserWithRole('member');

        $this->actingAs($member);

        $this->delete("/users/{$victim->id}")->assertStatus(403);
        $this->assertNull($victim->fresh()->deleted_at);
    }

    public function test_admin_can_update_roles_of_another_user(): void
    {
        $admin = $this->createUserWithRole('admin');
        $member = $this->createUserWithRole('member');
        $affiliateRole = Role::firstOrCreate(['role' => 'affiliate']);

        $this->actingAs($admin);

        $this->put("/users/{$member->id}", [
            'firstname' => $member->firstname,
            'lastname' => $member->lastname,
            'email' => $member->email,
            'roles' => [$affiliateRole->id],
        ]);

        $this->assertTrue($member->fresh()->load('roles')->hasRole('affiliate'));
    }
}
