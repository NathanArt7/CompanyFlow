<?php

namespace Database\Factories;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Entreprise;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $entreprise = Entreprise::factory()->create();

        return [
            'entreprise_id' => $entreprise->id,
            'room_id' => Room::factory()->for($entreprise, 'entreprise'),
            'user_id' => User::factory()->for($entreprise, 'entreprise'),
            'motif' => fake()->sentence(3),
            'nombre_participants' => fake()->numberBetween(1, 5),
            'date_reservation' => now()->addDays(fake()->numberBetween(1, 20))->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'statut' => ReservationStatus::CONFIRMEE,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'statut' => ReservationStatus::ANNULEE,
        ]);
    }
}
