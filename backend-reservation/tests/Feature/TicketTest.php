<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Equipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_a_ticket_on_any_equipment(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->nonEmpruntable()->create([
            'entreprise_id' => $admin->entreprise_id,
            'assigned_to' => null,
        ]);

        $response = $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Ne fonctionne plus',
        ]);

        $response->assertCreated();
        $this->assertEquals('EN_PANNE', $equipment->fresh()->etat);
    }

    public function test_super_employe_can_create_a_ticket_on_empruntable_equipment(): void
    {
        $superEmploye = $this->actingAsRole(RoleName::SUPER_EMPLOYE->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $superEmploye->entreprise_id]);

        $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Problème',
        ])->assertCreated();
    }

    public function test_super_employe_cannot_create_a_ticket_on_non_empruntable_assigned_to_someone_else(): void
    {
        $superEmploye = $this->actingAsRole(RoleName::SUPER_EMPLOYE->value);
        $otherUser = $this->makeUserWithRole(RoleName::EMPLOYE->value, ['entreprise_id' => $superEmploye->entreprise_id]);
        $equipment = Equipment::factory()->nonEmpruntable()->create([
            'entreprise_id' => $superEmploye->entreprise_id,
            'assigned_to' => $otherUser->id,
        ]);

        $response = $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Problème',
        ]);

        $response->assertStatus(403);
    }

    public function test_employe_can_create_a_ticket_on_non_empruntable_equipment_assigned_to_self(): void
    {
        $employe = $this->actingAsRole(RoleName::EMPLOYE->value);
        $equipment = Equipment::factory()->nonEmpruntable()->create([
            'entreprise_id' => $employe->entreprise_id,
            'assigned_to' => $employe->id,
        ]);

        $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Problème',
        ])->assertCreated();
    }

    public function test_employe_cannot_create_a_ticket_on_empruntable_equipment(): void
    {
        $employe = $this->actingAsRole(RoleName::EMPLOYE->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $employe->entreprise_id]);

        $response = $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Problème',
        ]);

        $response->assertStatus(403);
    }

    public function test_a_second_ticket_cannot_be_opened_on_equipment_with_an_open_ticket(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);

        $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Premier ticket',
        ])->assertCreated();

        $response = $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Second ticket',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('equipment_id');
    }

    public function test_technicien_accepting_a_ticket_notifies_the_creator_and_sets_equipment_in_maintenance(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);
        $creator = $this->makeUserWithRole(RoleName::SUPER_EMPLOYE->value, ['entreprise_id' => $admin->entreprise_id]);

        $ticket = \App\Models\Ticket::create([
            'entreprise_id' => $admin->entreprise_id,
            'equipment_id' => $equipment->id,
            'user_id' => $creator->id,
            'description' => 'En panne',
            'statut' => 'OUVERT',
        ]);

        $this->actingAsRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->patchJson("/api/tickets/{$ticket->id}/accept");

        $response->assertOk();
        $this->assertEquals('EN_MAINTENANCE', $equipment->fresh()->etat);
        $this->assertSame(1, $creator->notifications()->count());
    }

    public function test_technicien_closing_a_ticket_sets_final_equipment_state_and_notifies_creator(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);
        $creator = $this->makeUserWithRole(RoleName::EMPLOYE->value, ['entreprise_id' => $admin->entreprise_id]);

        $ticket = \App\Models\Ticket::create([
            'entreprise_id' => $admin->entreprise_id,
            'equipment_id' => $equipment->id,
            'user_id' => $creator->id,
            'description' => 'En panne',
            'statut' => 'EN_COURS',
        ]);

        $this->actingAsRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->patchJson("/api/tickets/{$ticket->id}/close", [
            'equipment_state' => 'FONCTIONNEL',
        ]);

        $response->assertOk();
        $this->assertEquals('DISPONIBLE', $equipment->fresh()->etat);
        $this->assertSame(1, $creator->notifications()->count());
    }

    public function test_employe_cannot_accept_a_ticket(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);
        $ticket = \App\Models\Ticket::create([
            'entreprise_id' => $admin->entreprise_id,
            'equipment_id' => $equipment->id,
            'user_id' => $admin->id,
            'description' => 'En panne',
            'statut' => 'OUVERT',
        ]);

        $this->actingAsRole(RoleName::EMPLOYE->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->patchJson("/api/tickets/{$ticket->id}/accept");

        $response->assertStatus(403);
    }
}
