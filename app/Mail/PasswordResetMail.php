<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crée une nouvelle instance.
     */
    public function __construct(
        public User $user,
        public string $resetLink
    ) {
    }

    /**
     * Construire le message.
     */
    public function build(): self
    {
        return $this
            ->subject('Réinitialisation de votre mot de passe')
            ->view('emails.password-reset');
    }
}