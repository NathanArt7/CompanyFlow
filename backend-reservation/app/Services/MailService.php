<?php

namespace App\Services;

use App\Mail\AccountActivationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;   

class MailService
{
    /**
     * Envoie l'e-mail d'activation.
     */
    public function sendActivationMail(User $user, string $activationLink): void
    {
        Mail::to($user->email)
            ->send(new AccountActivationMail($user, $activationLink));
    }

    /**
 * Envoie un e-mail de réinitialisation du mot de passe.
 */
public function sendPasswordResetMail(
    User $user,
    string $resetLink
): void
{
    Mail::to($user->email)
        ->send(
            new PasswordResetMail(
                $user,
                $resetLink
            )
        );
}
}