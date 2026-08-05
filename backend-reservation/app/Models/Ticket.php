<?php

namespace App\Models;

use App\Enums\Ticket\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'equipment_id',
        'user_id',
        'description',
        'statut',
    ];

    protected $casts = [
        'statut' => TicketStatus::class,
    ];

    /**
     * Entreprise propriétaire.
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(
            Entreprise::class
        );
    }

    /**
     * Matériel concerné. withTrashed() : un matériel supprimé doit rester
     * consultable dans l'historique des tickets.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(
            Equipment::class
        )->withTrashed();
    }

    /**
     * Utilisateur ayant créé le ticket. withTrashed() : un utilisateur
     * supprimé doit rester consultable dans l'historique des tickets.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        )->withTrashed();
    }
}
