<?php

namespace App\Services;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
 * Durée de validité du lien (en heures).
 */
private const RESET_LINK_DURATION_HOURS = 24;

/**
 * Génère un lien de réinitialisation.
 */
public function generateResetLink(
    User $user
): string
{
    $token = Str::random(64);

    $hashedToken = hash(
        'sha256',
        $token
    );

    $passwordReset = PasswordReset::where(
        'user_id',
        $user->id
    )->first();

    if ($passwordReset) {

        $passwordReset->update([

            'token' => $hashedToken,

            'expires_at' => now()->addHours(
                self::RESET_LINK_DURATION_HOURS
            ),

            'used_at' => null,

        ]);

    } else {

        PasswordReset::create([

            'user_id' => $user->id,

            'token' => $hashedToken,

            'expires_at' => now()->addHours(
                self::RESET_LINK_DURATION_HOURS
            ),

        ]);

    }

    return $token;
}

/**
 * Vérifie un lien de réinitialisation.
 */
public function verifyResetToken(
    string $token
): JsonResponse
{
    $passwordReset = $this->getValidReset(
        $token
    );

    return response()->json([

        'message' => 'Lien valide.',

        'email' => $passwordReset->user->email,

        'nom' => $passwordReset->user->nom,

        'prenom' => $passwordReset->user->prenom,

    ]);
}

/**
 * Réinitialise le mot de passe.
 */
public function resetPassword(
    array $data
): JsonResponse
{
    $passwordReset = $this->getValidReset(
        $data['token']
    );

    DB::transaction(function () use (
        $passwordReset,
        $data
    ) {

        $passwordReset->user->update([

            'password' => Hash::make(
                $data['password']
            ),

        ]);

        $passwordReset->update([

            'used_at' => now(),

        ]);

    });

    return response()->json([

        'message' => 'Mot de passe réinitialisé avec succès.',

    ]);
}

/**
 * Retourne une demande de réinitialisation valide.
 */
private function getValidReset(
    string $token
): PasswordReset
{
    $passwordReset = PasswordReset::with('user')
        ->where(
            'token',
            hash('sha256', $token)
        )
        ->first();

    if (! $passwordReset) {

        abort(
            response()->json([
                'message' => 'Lien de réinitialisation invalide.'
            ], 404)
        );

    }

    if ($passwordReset->used_at) {

        abort(
            response()->json([
                'message' => 'Ce lien a déjà été utilisé.'
            ], 409)
        );

    }

    if ($passwordReset->expires_at->isPast()) {

        abort(
            response()->json([
                'message' => 'Ce lien de réinitialisation a expiré.'
            ], 410)
        );

    }

    return $passwordReset;
}


}