<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_creating_a_room_produces_a_retrievable_activity_log_entry(): void
    {
        $superAdmin = $this->actingAsRole(RoleName::SUPER_ADMIN->value, ['nom' => 'Martin', 'prenom' => 'Julie']);

        $this->postJson('/api/rooms', [
            'nom' => 'Salle Log',
            'code' => 'LOG-1',
            'type' => 'MEETING',
            'capacite' => 10,
            'localisation' => 'Étage 1',
            'statut' => 'Disponible',
        ])->assertCreated();

        $response = $this->getJson('/api/activity-logs?module=SALLE');

        $response->assertOk();
        $descriptions = collect($response->json('data'))->pluck('description');
        $this->assertTrue($descriptions->contains(fn ($d) => str_contains($d, 'A créé la salle Salle Log')));
    }

    public function test_administrateur_cannot_access_activity_logs(): void
    {
        $this->actingAsRole(RoleName::ADMIN->value);

        $response = $this->getJson('/api/activity-logs?module=SALLE');

        $response->assertStatus(403);
    }

    public function test_activity_logs_are_isolated_per_entreprise(): void
    {
        $superAdminA = $this->actingAsRole(RoleName::SUPER_ADMIN->value);
        Room::factory()->for($superAdminA->entreprise, 'entreprise')->create(['nom' => 'Salle Entreprise A']);
        $this->postJson('/api/rooms', [
            'nom' => 'Nouvelle salle A',
            'code' => 'A-1',
            'type' => 'MEETING',
            'capacite' => 5,
            'localisation' => 'Ici',
            'statut' => 'Disponible',
        ])->assertCreated();

        $superAdminB = $this->actingAsRole(RoleName::SUPER_ADMIN->value);

        $response = $this->getJson('/api/activity-logs?module=SALLE');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    public function test_activity_logs_can_be_searched_by_actor_name(): void
    {
        $superAdmin = $this->actingAsRole(RoleName::SUPER_ADMIN->value, ['nom' => 'Dupont', 'prenom' => 'Alice']);

        $this->postJson('/api/rooms', [
            'nom' => 'Salle Recherche',
            'code' => 'SEARCH-1',
            'type' => 'MEETING',
            'capacite' => 5,
            'localisation' => 'Ici',
            'statut' => 'Disponible',
        ])->assertCreated();

        $match = $this->getJson('/api/activity-logs?module=SALLE&search=Alice+Dupont');
        $match->assertOk();
        $this->assertCount(1, $match->json('data'));

        $noMatch = $this->getJson('/api/activity-logs?module=SALLE&search=Personne');
        $noMatch->assertOk();
        $this->assertCount(0, $noMatch->json('data'));
    }
}
