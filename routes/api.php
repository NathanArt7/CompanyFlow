<?php

use App\Http\Controllers\Api\ActivationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EntrepriseController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post(
    '/entreprises',
    [EntrepriseController::class, 'store']
);

Route::get(
    '/account-activation/{token}',
    [ActivationController::class, 'verify']
);

Route::post(
    '/account-activation',
    [ActivationController::class, 'activate']
);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->middleware('throttle:5,1');;
Route::get('/reset-password/{token}', [PasswordResetController::class, 'verify']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);



/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentification
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/change-password', [AuthController::class, 'changePassword']);

    /*
    |--------------------------------------------------------------------------
    | Entreprises
    |--------------------------------------------------------------------------
    */

   Route::post(
    '/entreprise/configuration',
    [EntrepriseController::class, 'updateConfiguration']
);
    /*
    |--------------------------------------------------------------------------
    | Utilisateurs
    |--------------------------------------------------------------------------
    */
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::post('/users/{user}/resend-activation', [UserController::class, 'resendActivation']);
    
    /*
    |--------------------------------------------------------------------------
    | Salles
    |--------------------------------------------------------------------------
    */

    Route::post('/rooms', [RoomController::class, 'store'])
        ->middleware('permission:creer_salle');

    Route::get('/rooms', [RoomController::class, 'index'])
        ->middleware('permission:consulter_salles');

    Route::get('/rooms/{room}', [RoomController::class, 'show'])
        ->middleware('permission:consulter_salles');
});