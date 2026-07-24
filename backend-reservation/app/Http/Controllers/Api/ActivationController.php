<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ActivateAccountRequest;
use App\Http\Controllers\Controller;
use App\Services\ActivationService;

class ActivationController extends Controller
{
    public function __construct(
        private ActivationService $activationService
    ) {
    }

    /**
     * Vérifie un lien d'activation.
     */
    public function verify(string $token)
    {
        return $this->activationService
            ->verifyActivationToken($token);
    }
    public function activate(ActivateAccountRequest $request)
{
    return $this->activationService
        ->activateAccount($request->validated());
}
}