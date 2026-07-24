<?php

namespace App\Services;

use App\Enums\EntrepriseStatus;
use App\Models\Entreprise;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Enums\Reservation\DayOfWeek;
use App\Models\ReservationSetting;

class EntrepriseService
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
 * Création d'une entreprise.
 */
public function createEntreprise(array $data): Entreprise
{
    return DB::transaction(function () use ($data) {

        $entreprise = $this->createEntrepriseRecord();

        $this->userService->createFirstSuperAdmin(
            $entreprise,
            $data
        );

        return $entreprise;
    });
}

    /**
     * Crée l'entreprise.
     */
    private function createEntrepriseRecord(): Entreprise
    {
        $entreprise = Entreprise::create([

            'statut' => EntrepriseStatus::EN_ATTENTE,

        ]);

        $entreprise->reference =
            'ENT-' . str_pad(
                $entreprise->id,
                6,
                '0',
                STR_PAD_LEFT
            );

        $entreprise->save();

        return $entreprise;
    }

    /**
 * Configure une entreprise après la première connexion
 * du Super Administrateur.
 */
public function configureEntreprise(
    Entreprise $entreprise,
    array $data,
    ?UploadedFile $logo
): Entreprise
{
    if ($entreprise->statut === EntrepriseStatus::ACTIVE) {

    abort(409, 'Cette entreprise est déjà configurée.');

}
    return DB::transaction(function () use (
        $entreprise,
        $data,
        $logo
    ) {

        if ($logo) {

            $data['logo'] = $this->storeLogo($logo);

        }

        $this->completeEntrepriseProfile(
            $entreprise,
            $data
        );

         $this->initializeReservationSettings(
            $entreprise
        );

        $this->activateEntreprise(
            $entreprise
        );

        return $entreprise->fresh();
    });
}

/**
 * Stocke le logo de l'entreprise.
 */
private function storeLogo(
    UploadedFile $logo
): string
{
    return $logo->store(
        'logos',
        'public'
    );
}

/**
 * Complète les informations de l'entreprise.
 */
private function completeEntrepriseProfile(
    Entreprise $entreprise,
    array $data
): void
{
    $entreprise->update([

        'nom' => $data['nom'],

        'telephone' => $data['telephone'],

        'adresse' => $data['adresse'],

        'site_web' => $data['site_web'] ?? null,

        'logo' => $data['logo'] ?? $entreprise->logo,

    ]);
}

/**
 * Active définitivement l'entreprise.
 */
private function activateEntreprise(
    Entreprise $entreprise
): void
{
    $entreprise->update([

        'statut' => EntrepriseStatus::ACTIVE,

    ]);
}

/**
 * Initialise la configuration des réservations.
 */
private function initializeReservationSettings(
    Entreprise $entreprise
): void {

    if (
        $entreprise->reservationSetting()->exists()
    ) {
        return;
    }

    $reservationSetting =
        $entreprise
            ->reservationSetting()
            ->create([
                'reservation_buffer' => 0,
            ]);

    $this->createDefaultServiceHours(
        $reservationSetting
    );

}

/**
 * Crée les horaires de service par défaut.
 */
private function createDefaultServiceHours(
    ReservationSetting $reservationSetting
): void {

    foreach (
        DayOfWeek::cases()
        as $day
    ) {

        $reservationSetting
            ->serviceHours()
            ->create([

                'day_of_week' => $day,

                'is_open' => false,

                'start_time' => null,

                'end_time' => null,

            ]);

    }

}


}