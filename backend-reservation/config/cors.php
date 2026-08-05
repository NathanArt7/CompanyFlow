<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // FRONTEND_URL est déjà la variable utilisée pour construire les liens d'activation/
    // réinitialisation (config/app.php: frontend_url) : même source pour éviter que le
    // domaine de prod soit configuré à un seul endroit et oublié à l'autre.
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
