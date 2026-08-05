<?php

namespace App\Models;

use App\Enums\Reservation\CancellationReason;
use App\Enums\Reservation\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'room_id',
        'user_id',
        'motif',
        'nombre_participants',
        'date_reservation',
        'heure_debut',
        'heure_fin',
        'statut',
        'cancellation_reason',
        'cancelled_by',
        'reminder_sent_at',
    ];

    /**
     * Conversion automatique des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_reservation' => 'date',
        'statut' => ReservationStatus::class,
        'cancellation_reason' => CancellationReason::class,
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
     * Salle réservée. withTrashed() : une salle supprimée doit rester
     * consultable dans l'historique des réservations passées.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(
            Room::class
        )->withTrashed();
    }

    /**
     * Réservataire. withTrashed() : un utilisateur supprimé doit rester
     * consultable dans l'historique des réservations passées.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        )->withTrashed();
    }

    /**
     * Utilisateur ayant annulé la réservation.
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by'
        )->withTrashed();
    }

    /**
     * Matériels associés à la réservation.
     */
    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(
            Equipment::class,
            'reservation_equipments'
        )->withTimestamps();
    }
}