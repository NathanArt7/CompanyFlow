<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $activationLink
    ) {
    }

    /**
     * Objet de l'e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activation de votre compte',
        );
    }

    /**
     * Vue utilisée.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account-activation',
        );
    }

    /**
     * Pièces jointes.
     */
    public function attachments(): array
    {
        return [];
    }
}