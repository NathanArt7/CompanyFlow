<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationSetting extends Model
{
    /**
     * Les attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'reservation_buffer',
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
     * Horaires de service.
     */
    public function serviceHours(): HasMany
    {
        return $this->hasMany(
            ServiceHour::class
        );
    }
}