<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentCategory extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'nom',
        'description',
    ];

    /**
     * Entreprise propriétaire de la catégorie.
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    /**
     * Équipements appartenant à cette catégorie.
     */
    public function equipments(): HasMany
    {
           return $this->hasMany(Equipment::class, 'category_id');
    }
}