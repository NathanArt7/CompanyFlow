<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'nom' => 'Salle ' . fake()->unique()->bothify('??-###'),
            'code' => fake()->unique()->bothify('ROOM-####'),
            'type' => RoomType::MEETING,
            'capacite' => fake()->numberBetween(2, 50),
            'localisation' => fake()->streetAddress(),
            'description' => null,
            'statut' => RoomStatus::DISPONIBLE,
        ];
    }

    public function storage(): static
    {
        return $this->state(fn () => [
            'type' => RoomType::STORAGE,
            'capacite' => null,
        ]);
    }
}
