<?php

namespace Tests\Console;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendReservationRemindersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_it_sends_a_reminder_for_a_reservation_starting_in_20_minutes(): void
    {
        $organizer = User::factory()->create();
        $room = Room::factory()->for($organizer->entreprise, 'entreprise')->create();

        $reservation = Reservation::factory()->create([
            'entreprise_id' => $organizer->entreprise_id,
            'room_id' => $room->id,
            'user_id' => $organizer->id,
            'statut' => ReservationStatus::CONFIRMEE,
            'date_reservation' => now()->toDateString(),
            'heure_debut' => now()->addMinutes(20)->format('H:i:s'),
            'heure_fin' => now()->addMinutes(50)->format('H:i:s'),
        ]);

        $this->artisan('reservations:send-reminders')->assertExitCode(0);

        $this->assertSame(1, $organizer->notifications()->count());
        $this->assertNotNull($reservation->fresh()->reminder_sent_at);
    }

    public function test_it_does_not_send_a_duplicate_reminder_on_a_second_run(): void
    {
        $organizer = User::factory()->create();
        $room = Room::factory()->for($organizer->entreprise, 'entreprise')->create();

        Reservation::factory()->create([
            'entreprise_id' => $organizer->entreprise_id,
            'room_id' => $room->id,
            'user_id' => $organizer->id,
            'statut' => ReservationStatus::CONFIRMEE,
            'date_reservation' => now()->toDateString(),
            'heure_debut' => now()->addMinutes(20)->format('H:i:s'),
            'heure_fin' => now()->addMinutes(50)->format('H:i:s'),
        ]);

        $this->artisan('reservations:send-reminders');
        $this->artisan('reservations:send-reminders');

        $this->assertSame(1, $organizer->notifications()->count());
    }

    public function test_it_does_not_send_a_reminder_outside_the_20_minute_window(): void
    {
        $organizer = User::factory()->create();
        $room = Room::factory()->for($organizer->entreprise, 'entreprise')->create();

        Reservation::factory()->create([
            'entreprise_id' => $organizer->entreprise_id,
            'room_id' => $room->id,
            'user_id' => $organizer->id,
            'statut' => ReservationStatus::CONFIRMEE,
            'date_reservation' => now()->toDateString(),
            'heure_debut' => now()->addHours(3)->format('H:i:s'),
            'heure_fin' => now()->addHours(4)->format('H:i:s'),
        ]);

        $this->artisan('reservations:send-reminders');

        $this->assertSame(0, $organizer->notifications()->count());
    }
}
