<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [

        'nom',

        'code',

        'capacite',

        'localisation',

        'description',

        'statut',

    ];

    protected $casts = [

        'statut' => RoomStatus::class,

    ];
}