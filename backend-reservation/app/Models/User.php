<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
         'entreprise_id',
        'nom',
        'prenom',
        'email',
        'password',
        'role_id',
        'photo',
        'actif',
        'password_changed',
        'notification_preferences',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'actif'                    => 'boolean',
        'password_changed'         => 'boolean',
        'password'                 => 'hashed',
        'notification_preferences' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role
            ->permissions()
            ->where('nom', $permission)
            ->exists();
    }

    public function hasRole(string $role): bool
    {
        return $this->role->nom === $role;
    }

    /**
 * Activations de ce compte.
 */
    public function accountActivations(): HasMany
    {
    return $this->hasMany(AccountActivation::class);
    }

/**
 * Comptes créés par cet utilisateur.
 */
    public function createdActivations(): HasMany
    {
    return $this->hasMany(AccountActivation::class, 'created_by');
    }

    public function entreprise()
{
    return $this->belongsTo(Entreprise::class);
}

/**
 * Réinitialisation de mot de passe.
 */
public function passwordReset()
{
    return $this->hasOne(
        PasswordReset::class
    );
}

/**
 * Réservations créées par l'utilisateur.
 */
public function reservations(): HasMany
{
    return $this->hasMany(
        Reservation::class
    );
}

/**
 * Réservations annulées par l'utilisateur.
 */
public function cancelledReservations(): HasMany
{
    return $this->hasMany(
        Reservation::class,
        'cancelled_by'
    );
}
}