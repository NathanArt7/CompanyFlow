<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        if (!$user->actif) {
            return response()->json(['message' => 'Compte désactivé'], 403);
        }

        if (!$user->hasPermission($permission)) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        return $next($request);
    }
}