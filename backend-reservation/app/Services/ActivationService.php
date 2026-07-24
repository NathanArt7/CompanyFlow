<?php

namespace App\Services;

use App\Models\AccountActivation;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ActivationService
{
    /**
     * Durée de validité du lien d'activation (en heures).
     */
    private const ACTIVATION_LINK_DURATION_HOURS = 24;

    /**
     * Génère un lien d'activation et enregistre le token.
     */
    public function generateActivationLink(User $user, ?User $creator = null): string
    {
        $token = Str::random(64);

        $hashedToken = hash('sha256', $token);

        $activation = AccountActivation::where(
                'user_id',
                $user->id
            )->first();

        if (! $activation) {
    AccountActivation::create([
        'user_id' => $user->id,
        'created_by' => $creator?->id,
        'token' => $hashedToken,
        'expires_at' => now()->addHours(
            self::ACTIVATION_LINK_DURATION_HOURS
        ),
    ]);
}
else {

    $activation->update([
        'token' => $hashedToken,
        'expires_at' => now()->addHours(
            self::ACTIVATION_LINK_DURATION_HOURS
        ),
        'used_at' => null,
    ]);

}
      return $token;
    }
    /**
 * Vérifie la validité d'un token d'activation.
 */
public function verifyActivationToken(string $token): JsonResponse
{
    $activation = $this->getValidActivation($token);

    return response()->json([
        'message' => 'Lien valide.',
        'email'   => $activation->user->email,
        'nom'     => $activation->user->nom,
        'prenom'  => $activation->user->prenom,
    ]);
}

/**
 * Active un compte utilisateur.
 */
public function activateAccount(array $data): JsonResponse
{
    $activation = $this->getValidActivation($data['token']);

    DB::transaction(function () use ($activation, $data) {
        $activation->user->update([
            'password' => Hash::make($data['password']),
            'password_changed' => true,
            'actif' => true,
        ]);
        $activation->update([
            'used_at' => now(),
        ]);
    });

    return response()->json([
        'message' => 'Compte activé avec succès.'
    ]);
}
/**
 * Retourne une activation valide.
 */
private function getValidActivation(string $token): AccountActivation
{
    $activation = AccountActivation::with('user')
        ->where('token', hash('sha256', $token))
        ->first();
    if (!$activation) {
      
        abort(
            response()->json([
                'message' => 'Lien d’activation invalide.'
            ], 404)
        );
    }

    if ($activation->used_at) {
        abort(
            response()->json([
                'message' => 'Ce lien a déjà été utilisé.'
            ], 409)
        );
    }

    if ($activation->expires_at->isPast()) {
        abort(
            response()->json([
                'message' => 'Ce lien d’activation a expiré.'
            ], 410)
        );
    }

    return $activation;
}
}