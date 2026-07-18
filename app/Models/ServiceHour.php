<?php

namespace App\Models;

use App\Enums\Reservation\DayOfWeek;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceHour extends Model
{
    /**
     * Les attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reservation_setting_id',
        'day_of_week',
        'is_open',
        'start_time',
        'end_time',
    ];

    /**
     * Conversion automatique des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => DayOfWeek::class,
        'is_open' => 'boolean',
    ];

    /**
     * Configuration de réservation.
     */
    public function reservationSetting(): BelongsTo
    {
        return $this->belongsTo(
            ReservationSetting::class
        );
    }
}