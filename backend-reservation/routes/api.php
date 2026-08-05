<?php

use App\Http\Controllers\Api\ActivationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EntrepriseController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\EquipmentCategoryController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\ReservationSettingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationPreferenceController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\NotificationController;

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

    Route::put('/me', [AuthController::class, 'updateProfile'])
        ->middleware('permission:modifier_son_profil');

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

Route::get(
    '/entreprise',
    [EntrepriseController::class, 'show']
);

Route::put(
    '/entreprise',
    [EntrepriseController::class, 'update']
)->middleware(
    'permission:configurer_systeme'
);

/*
|--------------------------------------------------------------------------
| Préférences de notification
|--------------------------------------------------------------------------
*/

Route::get(
    '/notification-preferences',
    [NotificationPreferenceController::class, 'index']
);

Route::put(
    '/notification-preferences',
    [NotificationPreferenceController::class, 'update']
);

/*
|--------------------------------------------------------------------------
| Configuration des réservations
|--------------------------------------------------------------------------
*/

Route::get(
    '/reservation-settings',
    [ReservationSettingController::class, 'index']
);

Route::put(
    '/reservation-settings',
    [ReservationSettingController::class, 'update']
)->middleware(
    'permission:configurer_systeme'
);

    /*
    |--------------------------------------------------------------------------
    | Utilisateurs
    |--------------------------------------------------------------------------
    */
    Route::get('/roles', [RoleController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/stats', [UserController::class, 'stats']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::post('/users/{user}/resend-activation', [UserController::class, 'resendActivation']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Salles
    |--------------------------------------------------------------------------
    */

    Route::post('/rooms', [RoomController::class, 'store'])
        ->middleware('permission:creer_salle');

    Route::get('/rooms', [RoomController::class, 'index'])
        ->middleware('permission:consulter_salle');

    Route::get('/rooms/stats', [RoomController::class, 'stats'])
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

    // Lecture ouverte à tout utilisateur authentifié (scopée par entreprise_id dans le
    // service), même logique que /equipments : le Technicien a besoin de filtrer par
    // catégorie sans avoir de droit de gestion sur les catégories elles-mêmes.
    Route::get(
        '/equipment-categories',
        [EquipmentCategoryController::class, 'index']
    );

    Route::get(
        '/equipment-categories/{category}',
        [EquipmentCategoryController::class, 'show']
    );

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

    // Lecture ouverte à tout utilisateur authentifié (scopée par entreprise_id dans le service) :
    // les rôles qui n'ont que "emprunter_materiel" (Super Employé, Employé) ont besoin de
    // consulter la liste/disponibilité du matériel sans pour autant gérer les équipements.
    Route::get(
        '/equipments',
        [EquipmentController::class, 'index']
    );

    Route::get(
        '/equipments/stats',
        [EquipmentController::class, 'stats']
    );

    Route::get(
        '/equipments/{equipment}',
        [EquipmentController::class, 'show']
    );

    Route::put(
        '/equipments/{equipment}',
        [EquipmentController::class, 'update']
    )->middleware('permission:modifier_materiel');

    Route::patch(
        '/equipments/{equipment}/status',
        [EquipmentController::class, 'updateStatus']
    )->middleware('permission:modifier_etat_materiel');

    Route::delete(
        '/equipments/{equipment}',
        [EquipmentController::class, 'destroy']
    )->middleware('permission:supprimer_materiel');

    /*
    |--------------------------------------------------------------------------
    | Réservations
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reservations',
        [ReservationController::class, 'index']
    );

    Route::get(
        '/reservations/counts',
        [ReservationController::class, 'counts']
    );

    Route::get(
        '/reservations/summary',
        [ReservationController::class, 'summary']
    );

    Route::post(
        '/reservations',
        [ReservationController::class, 'store']
    );

    Route::get(
        '/reservations/{reservation}',
        [ReservationController::class, 'show']
    );

    Route::put(
        '/reservations/{reservation}',
        [ReservationController::class, 'update']
    );

    Route::patch(
        '/reservations/{reservation}/cancel',
        [ReservationController::class, 'cancel']
    );

    Route::prefix('availability')
    ->controller(AvailabilityController::class)
    ->group(function () {

        Route::get(
            '/week',
            'week'
        );

        Route::get(
            '/equipments',
            'availableEquipments'
        );

        Route::get(
            '/reservations',
            'dayReservations'
        );

    });

    /*
    |--------------------------------------------------------------------------
    | Tickets
    |--------------------------------------------------------------------------
    */

    Route::prefix('tickets')
    ->controller(TicketController::class)
    ->group(function () {

        Route::get(
            '/stats',
            'stats'
        );

        Route::get(
            '/',
            'index'
        );

        Route::post(
            '/',
            'store'
        )->middleware('permission:creer_ticket');

        Route::patch(
            '/{ticket}/accept',
            'accept'
        )->middleware('permission:accepter_ticket');

        Route::patch(
            '/{ticket}/close',
            'close'
        )->middleware('permission:cloturer_ticket');

    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard')
    ->controller(DashboardController::class)
    ->middleware('permission:voir_dashboard_global')
    ->group(function () {

        Route::get(
            '/stats',
            'stats'
        );

        Route::get(
            '/alerts',
            'alerts'
        );

    });

    /*
    |--------------------------------------------------------------------------
    | Journaux d'activité
    |--------------------------------------------------------------------------
    */

    Route::prefix('activity-logs')
    ->controller(ActivityLogController::class)
    ->middleware('permission:consulter_journaux')
    ->group(function () {

        Route::get(
            '/',
            'index'
        );

    });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::prefix('notifications')
    ->controller(NotificationController::class)
    ->group(function () {

        Route::get(
            '/',
            'index'
        );

        Route::get(
            '/unread-count',
            'unreadCount'
        );

        Route::patch(
            '/read-all',
            'markAllAsRead'
        );

        Route::patch(
            '/{notification}/read',
            'markAsRead'
        );

    });


}
);