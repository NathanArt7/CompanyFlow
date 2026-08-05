<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelReservationRequest extends FormRequest
{
   /**
 * Détermine si l'utilisateur est autorisé.
 */
public function authorize(): bool
{
    return true;
}

/**
 * Règles de validation.
 *
 * Le motif d'annulation n'est pas saisi par l'utilisateur : il est déduit
 * automatiquement (créateur ou administrateur) dans ReservationService.
 */
public function rules(): array
{
    return [];
}
}
