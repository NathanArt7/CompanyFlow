<?php

namespace App\Models;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'nom',
        'code',
        'capacite',
        'localisation',
        'description',
        'statut',
    ];

    protected $casts = [
        'type' => RoomType::class,
        'statut' => RoomStatus::class,
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }
}