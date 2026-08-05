<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class EquipmentCategoryTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_a_category(): void
    {
        $this->actingAsRole(RoleName::ADMIN->value);

        $response = $this->postJson('/api/equipment-categories', [
            'nom' => 'Ordinateurs',
            'description' => 'Postes fixes et portables',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('equipment_categories', ['nom' => 'Ordinateurs']);
    }

    public function test_technicien_cannot_create_a_category(): void
    {
        $this->actingAsRole(RoleName::TECHNICIEN->value);

        $response = $this->postJson('/api/equipment-categories', [
            'nom' => 'Ordinateurs',
        ]);

        $response->assertStatus(403);
    }

    public function test_technicien_can_read_categories_for_the_filter_dropdown(): void
    {
        $admin = $this->makeUserWithRole(RoleName::ADMIN->value);
        EquipmentCategory::factory()->for($admin->entreprise, 'entreprise')->create();
        $this->actingAsRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->getJson('/api/equipment-categories');

        $response->assertOk();
    }

    public function test_deleting_a_category_with_equipment_is_blocked(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $category = EquipmentCategory::factory()->for($admin->entreprise, 'entreprise')->create();
        Equipment::factory()->create([
            'entreprise_id' => $admin->entreprise_id,
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson("/api/equipment-categories/{$category->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('equipment_categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    public function test_deleting_an_empty_category_succeeds(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $category = EquipmentCategory::factory()->for($admin->entreprise, 'entreprise')->create();

        $response = $this->deleteJson("/api/equipment-categories/{$category->id}");

        $response->assertOk();
        $this->assertSoftDeleted('equipment_categories', ['id' => $category->id]);
    }
}
