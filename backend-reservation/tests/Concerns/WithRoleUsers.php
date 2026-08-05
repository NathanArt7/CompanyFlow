<?php

namespace Tests\Concerns;

use App\Enums\Reservation\DayOfWeek;
use App\Models\Entreprise;
use App\Models\ReservationSetting;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait WithRoleUsers
{
    /**
     * Crée un utilisateur actif portant le rôle donné et l'authentifie via Sanctum
     * pour les requêtes HTTP suivantes du test.
     */
    protected function actingAsRole(string $roleName, array $attributes = []): User
    {
        $user = $this->makeUserWithRole($roleName, $attributes);

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Crée un utilisateur actif portant le rôle donné, sans l'authentifier.
     */
    protected function makeUserWithRole(string $roleName, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role_id' => Role::where('nom', $roleName)->firstOrFail()->id,
        ], $attributes));
    }

    /**
     * Ouvre tous les jours de la semaine (08:00-18:00) pour l'entreprise donnée : la
     * création/modification de réservation échoue avec "firstOrFail" sans configuration.
     */
    protected function seedServiceHours(Entreprise $entreprise, string $start = '08:00:00', string $end = '18:00:00'): ReservationSetting
    {
        $setting = ReservationSetting::create([
            'entreprise_id' => $entreprise->id,
            'reservation_buffer' => 0,
        ]);

        foreach (DayOfWeek::cases() as $day) {

            $setting->serviceHours()->create([
                'day_of_week' => $day,
                'is_open' => true,
                'start_time' => $start,
                'end_time' => $end,
            ]);

        }

        return $setting;
    }
}
