<?php

namespace Database\Factories;

use App\Enums\RoleName;
use App\Models\Entreprise;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => Role::where('nom', RoleName::EMPLOYE->value)->firstOrFail()->id,
            'photo' => null,
            'actif' => true,
            'password_changed' => true,
            'notification_preferences' => null,
        ];
    }

    private function withRole(RoleName $role): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('nom', $role->value)->firstOrFail()->id,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->withRole(RoleName::SUPER_ADMIN);
    }

    public function admin(): static
    {
        return $this->withRole(RoleName::ADMIN);
    }

    public function superEmploye(): static
    {
        return $this->withRole(RoleName::SUPER_EMPLOYE);
    }

    public function employe(): static
    {
        return $this->withRole(RoleName::EMPLOYE);
    }

    public function technicien(): static
    {
        return $this->withRole(RoleName::TECHNICIEN);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['actif' => false]);
    }
}
