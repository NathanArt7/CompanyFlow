<?php

namespace Database\Factories;

use App\Enums\EntrepriseStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Entreprise>
 */
class EntrepriseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('ENT-####??'),
            'nom' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'telephone' => fake()->phoneNumber(),
            'adresse' => fake()->address(),
            'statut' => EntrepriseStatus::ACTIVE,
        ];
    }
}
