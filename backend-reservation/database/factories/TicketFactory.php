<?php

namespace Database\Factories;

use App\Enums\Ticket\TicketStatus;
use App\Models\Entreprise;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        $entreprise = Entreprise::factory()->create();

        return [
            'entreprise_id' => $entreprise->id,
            'equipment_id' => Equipment::factory()->create(['entreprise_id' => $entreprise->id]),
            'user_id' => User::factory()->for($entreprise, 'entreprise'),
            'description' => fake()->sentence(),
            'statut' => TicketStatus::OUVERT,
        ];
    }
}
