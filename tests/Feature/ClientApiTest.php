<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $trainerUser;
    private User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin       = User::factory()->create(['role' => 'admin']);
        $this->trainerUser = User::factory()->create(['role' => 'trainer']);
        $this->clientUser  = User::factory()->create(['role' => 'client']);
    }

    /** Admin can list all clients */
    public function test_admin_can_list_clients(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $response = $this->getJson('/api/clients');

        $response->assertOk()->assertJsonStructure(['data', 'meta']);
    }

    /** Client cannot list all clients */
    public function test_client_cannot_list_all_clients(): void
    {
        $this->actingAs($this->clientUser, 'sanctum');

        $this->getJson('/api/clients')->assertForbidden();
    }

    /** Admin can create a client */
    public function test_admin_can_create_client(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $membership = Membership::factory()->create();

        $response = $this->postJson('/api/clients', [
            'name'          => 'Test Cliente',
            'email'         => 'test@example.com',
            'password'      => 'password123',
            'weight'        => 70,
            'height'        => 175,
            'membership_id' => $membership->id,
        ]);

        $response->assertCreated()
                 ->assertJsonPath('name', 'Test Cliente');

        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'client']);
        $this->assertDatabaseHas('clients', ['membership_id' => $membership->id]);
    }

    /** Non-admin cannot create a client */
    public function test_non_admin_cannot_create_client(): void
    {
        $this->actingAs($this->clientUser, 'sanctum');

        $this->postJson('/api/clients', [
            'name'     => 'Fake',
            'email'    => 'fake@example.com',
            'password' => 'password123',
        ])->assertForbidden();
    }

    /** Membership status is returned correctly */
    public function test_client_has_correct_membership_status(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $membership = Membership::factory()->create(['duration_days' => 30]);
        $client = Client::factory()->create([
            'membership_id'         => $membership->id,
            'membership_expires_at' => now()->addDays(3), // expiring soon
        ]);

        $response = $this->getJson("/api/clients/{$client->id}");

        $response->assertOk()
                 ->assertJsonPath('membership_status', 'expiring');
    }
}
