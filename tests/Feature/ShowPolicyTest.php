<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Role;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowPolicyTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::firstOrCreate(['role' => $role])->id);
        return $user;
    }

    // -------------------------------------------------------------------------
    // viewAny : member/affiliate/admin → oui  |  sans rôle → non
    // -------------------------------------------------------------------------

    public function test_member_can_list_shows()
    {
        Sanctum::actingAs($this->createUserWithRole('member'));
        $this->getJson('/api/shows')->assertStatus(200);
    }

    public function test_affiliate_can_list_shows()
    {
        Sanctum::actingAs($this->createUserWithRole('affiliate'));
        $this->getJson('/api/shows')->assertStatus(200);
    }

    public function test_admin_can_list_shows()
    {
        Sanctum::actingAs($this->createUserWithRole('admin'));
        $this->getJson('/api/shows')->assertStatus(200);
    }

    public function test_user_without_role_cannot_list_shows()
    {
        Sanctum::actingAs(User::factory()->create()); // aucun rôle
        $this->getJson('/api/shows')->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // create : admin → oui  |  membre → non
    // -------------------------------------------------------------------------

    public function test_admin_can_create_show()
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($this->createUserWithRole('admin'));

        $this->postJson('/api/shows', [
            'title'       => 'Nouveau spectacle',
            'slug'        => 'nouveau-spectacle',
            'description' => 'Une description',
            'poster_url'  => 'https://example.com/poster.jpg',
            'duration'    => 90,
            'created_in'  => 2024,
            'location_id' => $location->id,
            'bookable'    => true,
        ])->assertStatus(201);
    }

    public function test_member_cannot_create_show()
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($this->createUserWithRole('member'));

        $this->postJson('/api/shows', [
            'title'       => 'Spectacle non autorisé',
            'slug'        => 'spectacle-non-autorise',
            'description' => 'Une description',
            'poster_url'  => 'https://example.com/poster.jpg',
            'duration'    => 90,
            'created_in'  => 2024,
            'location_id' => $location->id,
            'bookable'    => true,
        ])->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // update : admin → oui  |  membre → non
    // -------------------------------------------------------------------------

    public function test_admin_can_update_show()
    {
        $admin = $this->createUserWithRole('admin');
        $show  = Show::factory()->create();

        Sanctum::actingAs($admin);
        $this->putJson("/api/shows/{$show->id}", [
            'title' => 'Titre modifié',
        ])->assertStatus(200);
    }

    public function test_member_cannot_update_show()
    {
        $member = $this->createUserWithRole('member');
        $show   = Show::factory()->create();

        Sanctum::actingAs($member);
        $this->putJson("/api/shows/{$show->id}", [
            'title' => 'Tentative modification',
        ])->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // delete : admin → oui  |  membre → non
    // -------------------------------------------------------------------------

    public function test_admin_can_delete_show()
    {
        $admin = $this->createUserWithRole('admin');
        $show  = Show::factory()->create();

        Sanctum::actingAs($admin);
        $this->deleteJson("/api/shows/{$show->id}")->assertStatus(200);
    }

    public function test_member_cannot_delete_show()
    {
        $member = $this->createUserWithRole('member');
        $show   = Show::factory()->create();

        Sanctum::actingAs($member);
        $this->deleteJson("/api/shows/{$show->id}")->assertStatus(403);
    }
}
