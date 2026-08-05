<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

class NotificationService
{
    /**
     * Notifie un utilisateur si sa préférence pour ce type de notification est activée
     * (activée par défaut, même comportement que UserService::defaultNotificationPreferences()).
     */
    public function notify(
        User $recipient,
        string $preferenceKey,
        Notification $notification
    ): void {

        $enabled = ($recipient->notification_preferences ?? [])[$preferenceKey] ?? true;

        if (! $enabled) {
            return;
        }

        $recipient->notify($notification);

    }

    /**
     * Notifie plusieurs utilisateurs, chacun selon sa propre préférence.
     */
    public function notifyMany(
        iterable $recipients,
        string $preferenceKey,
        Notification $notification
    ): void {

        foreach ($recipients as $recipient) {

            $this->notify(
                $recipient,
                $preferenceKey,
                $notification
            );

        }

    }

    /**
     * Retourne les Techniciens de l'entreprise, en excluant éventuellement l'auteur
     * de l'action (pour ne pas notifier quelqu'un de son propre changement).
     */
    public function technicienRecipients(
        int $entrepriseId,
        ?int $excludeUserId = null
    ): Collection {

        return User::query()
            ->where(
                'entreprise_id',
                $entrepriseId
            )
            ->whereHas(
                'role',
                fn ($query) => $query->where('nom', RoleName::TECHNICIEN->value)
            )
            ->when(
                $excludeUserId,
                fn ($query) => $query->where('id', '!=', $excludeUserId)
            )
            ->get();

    }
}
