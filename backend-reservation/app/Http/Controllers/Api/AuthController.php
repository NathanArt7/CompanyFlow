<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
  public function __construct(
      private UserService $userService
  ) {
  }

  public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);
    
     if (! Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {

            return response()->json([
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

    $user = Auth::user();

    if (! $user->actif) {

        Auth::logout();

        return response()->json([
            'message' => "Votre compte a été désactivé. Contactez votre administrateur pour plus d'informations.",
            'code' => 'ACCOUNT_DISABLED',
        ], 403);

    }

    $user->load('entreprise');

    // Supprime les anciens tokens (optionnel)
    $user->tokens()->delete();

    // Création d'un nouveau token Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Connexion réussie',
        'token'   => $token,
        'user'    => [
            'id'                    => $user->id,
            'nom'                   => $user->nom,
            'prenom'                => $user->prenom,
            'email'                 => $user->email,
            'role_id'               => $user->role_id,
            'entreprise_configured' => $user->entreprise?->statut?->isActive() ?? false,
        ]
    ]);
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['role', 'entreprise']);
        return response()->json([
            'id'                    => $user->id,
            'nom'                   => $user->nom,
            'prenom'                => $user->prenom,
            'email'                 => $user->email,
            'role'                  => $user->role->nom,
            'photo'                 => $user->photo,
            'actif'                 => $user->actif,
            'password_changed'      => $user->password_changed,
            'permissions'           => $user->role->permissions->pluck('nom'),
            'entreprise_configured' => $user->entreprise?->statut?->isActive() ?? false,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateOwnProfile(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => [
                'id'                    => $user->id,
                'nom'                   => $user->nom,
                'prenom'                => $user->prenom,
                'email'                 => $user->email,
                'role'                  => $user->role->nom,
                'photo'                 => $user->photo,
                'actif'                 => $user->actif,
                'password_changed'      => $user->password_changed,
                'permissions'           => $user->role->permissions->pluck('nom'),
                'entreprise_configured' => $user->entreprise?->statut?->isActive() ?? false,
            ],
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password:sanctum',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password'         => bcrypt($request->password),
            'password_changed' => true,
        ]);

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }
}