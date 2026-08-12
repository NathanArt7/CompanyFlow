<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
     * URL publique de la photo de profil (stockée sur un disque S3-compatible,
     * ex. Cloudinary — nécessaire car le disque local de Render n'est pas
     * persistant : tout fichier écrit sur le conteneur disparaît au redéploiement).
     */
    public function photoUrl(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        // Ne doit jamais faire planter la requête appelante (ex. /api/me) :
        // une photo orpheline ou une erreur Cloudinary ponctuelle ne doit
        // dégrader que l'affichage de l'avatar, pas casser l'authentification.
        try {
            return Storage::disk('cloudinary')->url($this->photo);
        } catch (\Throwable $e) {
            Log::warning('Impossible de générer l\'URL de la photo de profil.', [
                'user_id' => $this->id,
                'photo' => $this->photo,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
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