<?php

namespace App\Models;

use App\Enums\Equipment\EquipmentUsageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */

     /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'equipments';

    protected $fillable = [
        'entreprise_id',
        'category_id',
        'storage_room_id',
        'assigned_to',
        'nom',
        'code',
        'marque',
        'modele',
        'localisation',
        'numero_serie',
        'usage_type',
        'etat',
    ];

    /**
     * Conversion automatique des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_type' => EquipmentUsageType::class,
    ];

    /**
     * Entreprise propriétaire du matériel.
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(
            Entreprise::class
        );
    }

    /**
     * Catégorie du matériel.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            EquipmentCategory::class,
            'category_id'
        );
    }

    /**
     * Salle de stockage.
     */
    public function storageRoom(): BelongsTo
    {
        return $this->belongsTo(
            Room::class,
            'storage_room_id'
        );
    }

    /**
     * Utilisateur auquel le matériel est affecté.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    /**
 * Réservations utilisant ce matériel.
 */
    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(
            Reservation::class,
            'reservation_equipments'
        )->withTimestamps();
    }
}