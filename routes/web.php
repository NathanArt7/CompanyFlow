<?php

use Illuminate\Support\Facades\Route;
use App\Mail\AccountActivationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-mail', function () {

    $user = User::find(1);

    $activationLink = 'http://localhost:8000/api/account-activation/test-token';

    Mail::to('ton@email.com')
        ->send(new AccountActivationMail($user, $activationLink));

    return 'Email envoyé !';
});