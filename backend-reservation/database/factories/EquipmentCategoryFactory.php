<?php

namespace Database\Factories;

use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EquipmentCategory>
 */
class EquipmentCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'nom' => fake()->unique()->word(),
            'description' => fake()->sentence(),
        ];
    }
}
