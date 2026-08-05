<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_a_room(): void
    {
        $this->actingAsRole(RoleName::ADMIN->value);

        $response = $this->postJson('/api/rooms', [
            'nom' => 'Salle Test',
            'code' => 'SAL-TEST-1',
            'type' => 'MEETING',
            'capacite' => 10,
            'localisation' => 'Étage 1',
            'statut' => 'Disponible',
        ]);

        $response->assertCreated()
            ->assertJsonPath('room.nom', 'Salle Test');

        $this->assertDatabaseHas('rooms', ['code' => 'SAL-TEST-1']);
    }

    public function test_employe_cannot_create_a_room(): void
    {
        $this->actingAsRole(RoleName::EMPLOYE->value);

        $response = $this->postJson('/api/rooms', [
            'nom' => 'Salle Test',
            'code' => 'SAL-TEST-2',
            'type' => 'MEETING',
            'capacite' => 10,
            'localisation' => 'Étage 1',
            'statut' => 'Disponible',
        ]);

        $response->assertStatus(403);
    }

    public function test_room_list_is_scoped_to_the_connected_user_entreprise(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        Room::factory()->for($admin->entreprise, 'entreprise')->create(['nom' => 'Salle Mine']);
        Room::factory()->create(['nom' => 'Salle Autre Entreprise']);

        $response = $this->getJson('/api/rooms');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('nom');
        $this->assertTrue($names->contains('Salle Mine'));
        $this->assertFalse($names->contains('Salle Autre Entreprise'));
    }

    public function test_updating_a_room_logs_a_precise_diff_description(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $room = Room::factory()->for($admin->entreprise, 'entreprise')->create([
            'nom' => 'Salle B',
            'statut' => \App\Enums\RoomStatus::DISPONIBLE,
        ]);

        $response = $this->putJson("/api/rooms/{$room->id}", [
            'nom' => 'Salle B',
            'code' => $room->code,
            'type' => 'MEETING',
            'capacite' => $room->capacite,
            'localisation' => $room->localisation,
            'statut' => 'En maintenance',
        ]);

        $response->assertOk();

        $log = ActivityLog::where('subject_id', $room->id)
            ->where('action', 'room.updated')
            ->firstOrFail();

        $this->assertStringContainsString('le statut de « Disponible » à « En maintenance »', $log->description);
    }

    public function test_deleting_a_room_cancels_future_reservations_and_notifies_organizer(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $organizer = $this->makeUserWithRole(RoleName::SUPER_EMPLOYE->value, [
            'entreprise_id' => $admin->entreprise_id,
        ]);
        $room = Room::factory()->for($admin->entreprise, 'entreprise')->create();

        $futureReservation = Reservation::factory()->create([
            'entreprise_id' => $admin->entreprise_id,
            'room_id' => $room->id,
            'user_id' => $organizer->id,
            'date_reservation' => now()->addDays(5)->toDateString(),
        ]);

        $pastReservation = Reservation::factory()->create([
            'entreprise_id' => $admin->entreprise_id,
            'room_id' => $room->id,
            'user_id' => $organizer->id,
            'date_reservation' => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->deleteJson("/api/rooms/{$room->id}");

        $response->assertOk();

        $futureReservation->refresh();
        $pastReservation->refresh();

        $this->assertEquals('ANNULEE', $futureReservation->statut->value);
        $this->assertEquals('ROOM_DELETED', $futureReservation->cancellation_reason->value);
        $this->assertEquals('CONFIRMEE', $pastReservation->statut->value);

        $this->assertSame(1, $organizer->notifications()->count());
        $this->assertSoftDeleted('rooms', ['id' => $room->id]);
    }
}
