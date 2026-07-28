<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Services\MailService;
use App\Services\PasswordResetService;

class PasswordResetController extends Controller
{
    public function __construct(
    private PasswordResetService $passwordResetService,
    private MailService $mailService
) {
}

    public function forgotPassword(
    ForgotPasswordRequest $request
): JsonResponse
{
    $user = User::where(
        'email',
        $request->validated('email')
    )->first();

    if (
        $user &&
        $user->actif &&
        $user->password_changed
    ) {

        $token = $this->passwordResetService
            ->generateResetLink($user);

        $resetLink = rtrim(config('app.frontend_url'), '/')
            . '/reset-password/' . $token;

        $this->mailService
            ->sendPasswordResetMail(
                $user,
                $resetLink
            );
    }

    return response()->json([
        'message' =>
            'Si un compte est associé à cette adresse e-mail, un lien de réinitialisation a été envoyé.'
    ]);
}

public function verify(
    string $token
): JsonResponse
{
    return $this->passwordResetService
        ->verifyResetToken($token);
}

public function reset(
    ResetPasswordRequest $request
): JsonResponse
{
    return $this->passwordResetService
        ->resetPassword(
            $request->validated()
        );
}
}
