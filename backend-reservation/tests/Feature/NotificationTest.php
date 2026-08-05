<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Equipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_disabling_the_systeme_preference_blocks_ticket_notifications(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $technicienWithPrefOff = $this->makeUserWithRole(RoleName::TECHNICIEN->value, [
            'entreprise_id' => $admin->entreprise_id,
            'notification_preferences' => ['reservations' => true, 'rappels' => true, 'systeme' => false, 'rapports_hebdomadaires' => false],
        ]);
        $technicienWithDefaultPrefs = $this->makeUserWithRole(RoleName::TECHNICIEN->value, [
            'entreprise_id' => $admin->entreprise_id,
        ]);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);

        $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Panne',
        ])->assertCreated();

        $this->assertSame(0, $technicienWithPrefOff->notifications()->count());
        $this->assertSame(1, $technicienWithDefaultPrefs->notifications()->count());
    }

    public function test_unread_count_and_mark_as_read(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $technicien = $this->makeUserWithRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);

        $this->postJson('/api/tickets', [
            'equipment_id' => $equipment->id,
            'description' => 'Panne',
        ])->assertCreated();

        Sanctum::actingAs($technicien);

        $this->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJson(['count' => 1]);

        $notificationId = $technicien->notifications()->first()->id;

        $this->patchJson("/api/notifications/{$notificationId}/read")->assertOk();

        $this->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJson(['count' => 0]);
    }

    public function test_mark_all_as_read(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $technicien = $this->makeUserWithRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);
        $equipmentA = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);
        $equipmentB = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);

        $this->postJson('/api/tickets', ['equipment_id' => $equipmentA->id, 'description' => 'Panne A'])->assertCreated();
        $this->postJson('/api/tickets', ['equipment_id' => $equipmentB->id, 'description' => 'Panne B'])->assertCreated();

        Sanctum::actingAs($technicien);

        $this->assertSame(2, $technicien->unreadNotifications()->count());

        $this->patchJson('/api/notifications/read-all')->assertOk();

        $this->assertSame(0, $technicien->unreadNotifications()->count());
    }

    public function test_cannot_mark_someone_elses_notification_as_read(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $technicienA = $this->makeUserWithRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);
        $technicienB = $this->makeUserWithRole(RoleName::TECHNICIEN->value, ['entreprise_id' => $admin->entreprise_id]);
        $equipment = Equipment::factory()->create(['entreprise_id' => $admin->entreprise_id]);

        $this->postJson('/api/tickets', ['equipment_id' => $equipment->id, 'description' => 'Panne'])->assertCreated();

        $notificationOfA = $technicienA->notifications()->firstOrFail();

        Sanctum::actingAs($technicienB);

        $response = $this->patchJson("/api/notifications/{$notificationOfA->id}/read");

        $response->assertStatus(404);
        $this->assertNull($notificationOfA->fresh()->read_at);
    }
}
