<?php

namespace App\Models;

use App\Enums\EntrepriseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entreprise extends Model
{
    protected $fillable = [
        'reference',
        'created_by',
        'nom',
        'email',
        'telephone',
        'adresse',
        'site_web',
        'logo',
        'statut',
    ];

    protected $casts = [
        'statut' => EntrepriseStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}