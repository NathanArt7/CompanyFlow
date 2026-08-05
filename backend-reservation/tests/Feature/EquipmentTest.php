<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_an_empruntable_equipment_with_a_storage_room(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $category = EquipmentCategory::factory()->for($admin->entreprise, 'entreprise')->create();
        $storageRoom = Room::factory()->storage()->for($admin->entreprise, 'entreprise')->create();

        $response = $this->postJson('/api/equipments', [
            'category_id' => $category->id,
            'nom' => 'PC portable',
            'code' => 'EQP-001',
            'marque' => 'Dell',
            'modele' => 'XPS',
            'usage_type' => 'EMPRUNTABLE',
            'etat' => 'DISPONIBLE',
            'storage_room_id' => $storageRoom->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('equipments', ['code' => 'EQP-001']);
    }

    public function test_creating_an_empruntable_equipment_without_storage_room_fails(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $category = EquipmentCategory::factory()->for($admin->entreprise, 'entreprise')->create();

        $response = $this->postJson('/api/equipments', [
            'category_id' => $category->id,
            'nom' => 'PC portable',
            'code' => 'EQP-002',
            'marque' => 'Dell',
            'modele' => 'XPS',
            'usage_type' => 'EMPRUNTABLE',
            'etat' => 'DISPONIBLE',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('storage_room_id');
    }

    public function test_technicien_cannot_create_equipment(): void
    {
        $this->actingAsRole(RoleName::TECHNICIEN->value);

        $response = $this->postJson('/api/equipments', ['nom' => 'PC']);

        $response->assertStatus(403);
    }

    public function test_technicien_can_change_equipment_status(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create([
            'entreprise_id' => $admin->entreprise_id,
            'etat' => 'DISPONIBLE',
        ]);
        $this->actingAsRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->patchJson("/api/equipments/{$equipment->id}/status", [
            'etat' => 'EN_MAINTENANCE',
        ]);

        $response->assertOk();
        $this->assertEquals('EN_MAINTENANCE', $equipment->fresh()->etat);
    }

    public function test_equipment_status_change_notifies_other_techniciens_but_not_the_actor(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create([
            'entreprise_id' => $admin->entreprise_id,
            'etat' => 'DISPONIBLE',
        ]);
        $otherTechnicien = $this->makeUserWithRole(RoleName::TECHNICIEN->value, [
            'entreprise_id' => $admin->entreprise_id,
        ]);
        $actingTechnicien = $this->actingAsRole(RoleName::TECHNICIEN->value, [
            'entreprise_id' => $admin->entreprise_id,
        ]);

        $this->patchJson("/api/equipments/{$equipment->id}/status", [
            'etat' => 'EN_PANNE',
        ])->assertOk();

        $this->assertSame(1, $otherTechnicien->notifications()->count());
        $this->assertSame(0, $actingTechnicien->notifications()->count());
    }

    public function test_deleting_equipment_requires_permission(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);
        $this->actingAsRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->deleteJson("/api/equipments/{$equipment->id}");

        $response->assertStatus(403);
    }
}
