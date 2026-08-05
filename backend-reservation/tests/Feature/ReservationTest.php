<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function nextMonday(): \Carbon\Carbon
    {
        return now()->next(\Carbon\Carbon::MONDAY);
    }

    public function test_admin_can_create_a_reservation(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $this->seedServiceHours($admin->entreprise);
        $room = Room::factory()->for($admin->entreprise, 'entreprise')->create(['capacite' => 20]);

        $response = $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'Réunion équipe',
            'nombre_participants' => 5,
            'date_reservation' => $this->nextMonday()->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('reservations', ['motif' => 'Réunion équipe']);
    }

    public function test_employe_cannot_create_a_reservation(): void
    {
        $employe = $this->actingAsRole(RoleName::EMPLOYE->value);
        $this->seedServiceHours($employe->entreprise);
        $room = Room::factory()->for($employe->entreprise, 'entreprise')->create();

        $response = $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'Réunion',
            'nombre_participants' => 2,
            'date_reservation' => $this->nextMonday()->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
        ]);

        $response->assertStatus(403);
    }

    public function test_overlapping_reservation_is_rejected(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $this->seedServiceHours($admin->entreprise);
        $room = Room::factory()->for($admin->entreprise, 'entreprise')->create(['capacite' => 20]);
        $date = $this->nextMonday()->toDateString();

        $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'Première réservation',
            'nombre_participants' => 5,
            'date_reservation' => $date,
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
        ])->assertCreated();

        $response = $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'Réservation en conflit',
            'nombre_participants' => 5,
            'date_reservation' => $date,
            'heure_debut' => '09:30',
            'heure_fin' => '10:30',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('room_id');
    }

    public function test_reservation_exceeding_room_capacity_is_rejected(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $this->seedServiceHours($admin->entreprise);
        $room = Room::factory()->for($admin->entreprise, 'entreprise')->create(['capacite' => 3]);

        $response = $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'Trop de monde',
            'nombre_participants' => 10,
            'date_reservation' => $this->nextMonday()->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('nombre_participants');
    }

    public function test_self_cancellation_does_not_create_a_notification(): void
    {
        $organizer = $this->actingAsRole(RoleName::SUPER_EMPLOYE->value);
        $this->seedServiceHours($organizer->entreprise);
        $room = Room::factory()->for($organizer->entreprise, 'entreprise')->create(['capacite' => 20]);

        $created = $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'À annuler',
            'nombre_participants' => 2,
            'date_reservation' => $this->nextMonday()->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
        ])->json('data');

        $this->patchJson("/api/reservations/{$created['id']}/cancel")->assertOk();

        $this->assertSame(0, $organizer->notifications()->count());
    }

    public function test_cancellation_by_someone_else_notifies_the_organizer(): void
    {
        $organizer = $this->actingAsRole(RoleName::SUPER_EMPLOYE->value);
        $entreprise = $organizer->entreprise;
        $this->seedServiceHours($entreprise);
        $room = Room::factory()->for($entreprise, 'entreprise')->create(['capacite' => 20]);

        $created = $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'motif' => 'À annuler par un tiers',
            'nombre_participants' => 2,
            'date_reservation' => $this->nextMonday()->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
        ])->json('data');

        $this->actingAsRole(RoleName::ADMIN->value, ['entreprise_id' => $entreprise->id]);

        $this->patchJson("/api/reservations/{$created['id']}/cancel")->assertOk();

        $this->assertSame(1, $organizer->notifications()->count());
    }
}
