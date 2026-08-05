<?php

namespace Database\Factories;

use App\Enums\Equipment\EquipmentUsageType;
use App\Models\Entreprise;
use App\Models\EquipmentCategory;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    public function definition(): array
    {
        $entreprise = Entreprise::factory()->create();

        return [
            'entreprise_id' => $entreprise->id,
            'category_id' => EquipmentCategory::factory()->for($entreprise, 'entreprise'),
            'storage_room_id' => Room::factory()->storage()->for($entreprise, 'entreprise'),
            'assigned_to' => null,
            'nom' => fake()->words(2, true),
            'code' => fake()->unique()->bothify('EQP-####'),
            'marque' => fake()->company(),
            'modele' => fake()->bothify('Model-###'),
            'localisation' => null,
            'numero_serie' => null,
            'usage_type' => EquipmentUsageType::EMPRUNTABLE,
            'etat' => 'DISPONIBLE',
        ];
    }

    /**
     * Matériel non empruntable : pas de salle de stockage, localisation obligatoire,
     * pas d'assignation par défaut (le storage_room_id du parent doit être annulé).
     */
    public function nonEmpruntable(): static
    {
        return $this->state(fn () => [
            'usage_type' => EquipmentUsageType::NON_EMPRUNTABLE,
            'storage_room_id' => null,
            'localisation' => fake()->streetAddress(),
            'etat' => 'FONCTIONNEL',
        ]);
    }
}
