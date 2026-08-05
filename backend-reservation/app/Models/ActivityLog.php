<?php

namespace App\Models;

use App\Enums\ActivityLog\ActivityModule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'entreprise_id',
        'user_id',
        'module',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'metadata',
    ];

    protected $casts = [
        'module' => ActivityModule::class,
        'metadata' => 'array',
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
     * Auteur de l'action. withTrashed() : un utilisateur supprimé doit
     * rester consultable dans l'historique des journaux.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        )->withTrashed();
    }

    /**
     * Entité concernée par l'action (réservation, salle, matériel...).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
