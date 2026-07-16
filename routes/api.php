<?php

use App\Http\Controllers\Api\ActivationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EntrepriseController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\EquipmentCategoryController;
use App\Http\Controllers\Api\EquipmentController;

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
        ->middleware('permission:consulter_salle');

    Route::get('/rooms/{room}', [RoomController::class, 'show'])
        ->middleware('permission:consulter_salle');

    Route::put('/rooms/{room}', [RoomController::class, 'update'])
    ->middleware('permission:modifier_salle');

    Route::patch('/rooms/{room}/status', [RoomController::class, 'updateStatus'])
    ->middleware('permission:bloquer_salle');

    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])
    ->middleware('permission:supprimer_salle');

        /*
    |--------------------------------------------------------------------------
    | Catégories de matériel
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/equipment-categories',
        [EquipmentCategoryController::class, 'store']
    )->middleware('permission:creer_categorie_materiel');

    Route::get(
        '/equipment-categories',
        [EquipmentCategoryController::class, 'index']
    )->middleware('permission:consulter_categorie_materiel');

    Route::get(
        '/equipment-categories/{category}',
        [EquipmentCategoryController::class, 'show']
    )->middleware('permission:consulter_categorie_materiel');

    Route::put(
        '/equipment-categories/{category}',
        [EquipmentCategoryController::class, 'update']
    )->middleware('permission:modifier_categorie_materiel');

    Route::delete(
        '/equipment-categories/{category}',
        [EquipmentCategoryController::class, 'destroy']
    )->middleware('permission:supprimer_categorie_materiel');

    /*
|--------------------------------------------------------------------------
| Matériels
|--------------------------------------------------------------------------
*/

    Route::post(
        '/equipments',
        [EquipmentController::class, 'store']
    )->middleware('permission:creer_materiel');

    Route::get(
        '/equipments',
        [EquipmentController::class, 'index']
    )->middleware('permission:consulter_materiel');

    Route::get(
        '/equipments/{equipment}',
        [EquipmentController::class, 'show']
    )->middleware('permission:consulter_materiel');

    Route::put(
        '/equipments/{equipment}',
        [EquipmentController::class, 'update']
    )->middleware('permission:modifier_materiel');

    Route::delete(
        '/equipments/{equipment}',
        [EquipmentController::class, 'destroy']
    )->middleware('permission:supprimer_materiel');

}
);