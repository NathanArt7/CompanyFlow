<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Equipment\EquipmentUsageType;
use App\Enums\Equipment\EmpruntableEquipmentStatus;
use App\Enums\Equipment\NonEmpruntableEquipmentStatus;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
     /**
     * Détermine si l'utilisateur est autorisé
     * à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

    'category_id' => [
        'required',
        'integer',
        'exists:equipment_categories,id',
    ],

    'nom' => [
        'required',
        'string',
        'max:255',
    ],

    'code' => [
        'required',
        'string',
        'max:100',
    ],

    'marque' => [
        'required',
        'string',
        'max:255',
    ],

    'modele' => [
        'required',
        'string',
        'max:255',
    ],

    'numero_serie' => [
        'nullable',
        'string',
        'max:255',
    ],

    'usage_type' => [
        'required',
         Rule::in(EquipmentUsageType::values()),
    ],

    'etat' => [
    'required',
    'string',
    'max:100',
],

    'storage_room_id' => [
        'nullable',
        'integer',
        'exists:rooms,id',
    ],

    'localisation' => [
        'nullable',
        'string',
        'max:255',
    ],

    'assigned_to' => [
        'nullable',
        'integer',
        'exists:users,id',
    ],

];
    }

    /**
 * Ajoute les règles métier après la validation classique.
 */
public function withValidator(Validator $validator): void
{
    $validator->after(function (Validator $validator) {

        $this->validateUsageTypeRules($validator);

        $this->validateEquipmentStatus($validator);

    });
}

/**
 * Vérifie les règles liées au type d'utilisation.
 */
private function validateUsageTypeRules(
    Validator $validator
): void {

    if (
        $this->input('usage_type') ===
        EquipmentUsageType::EMPRUNTABLE->value
    ) {

        if (!$this->filled('storage_room_id')) {

            $validator->errors()->add(
                'storage_room_id',
                "La salle de stockage est obligatoire pour un matériel empruntable."
            );

        }

        if ($this->filled('localisation')) {

            $validator->errors()->add(
                'localisation',
                "La localisation est interdite pour un matériel empruntable."
            );

        }

        if ($this->filled('assigned_to')) {

            $validator->errors()->add(
                'assigned_to',
                "Un matériel empruntable ne peut pas être affecté à un utilisateur."
            );

        }

        return;

    }

    if (
        $this->input('usage_type') ===
        EquipmentUsageType::NON_EMPRUNTABLE->value
    ) {

        if (!$this->filled('localisation')) {

            $validator->errors()->add(
                'localisation',
                "La localisation est obligatoire pour un matériel non empruntable."
            );

        }

        if ($this->filled('storage_room_id')) {

            $validator->errors()->add(
                'storage_room_id',
                "Une salle de stockage est interdite pour un matériel non empruntable."
            );

        }

    }

}

/**
 * Vérifie que l'état correspond au type d'utilisation.
 */
private function validateEquipmentStatus(
    Validator $validator
): void {

    $usageType = $this->input('usage_type');

    $status = $this->input('etat');

    if (
        $usageType ===
        EquipmentUsageType::EMPRUNTABLE->value
    ) {

        $allowedStatuses = EmpruntableEquipmentStatus::values();

        if (!in_array($status, $allowedStatuses, true)) {

            $validator->errors()->add(
                'etat',
                "L'état sélectionné est invalide pour un matériel empruntable."
            );

        }

        return;
    }

    if (
        $usageType ===
        EquipmentUsageType::NON_EMPRUNTABLE->value
    ) {

       $allowedStatuses = NonEmpruntableEquipmentStatus::values();

        if (!in_array($status, $allowedStatuses, true)) {

            $validator->errors()->add(
                'etat',
                "L'état sélectionné est invalide pour un matériel non empruntable."
            );

        }

    }

}

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [

    'category_id.required' =>
        'La catégorie est obligatoire.',

    'category_id.integer' =>
        'La catégorie est invalide.',

    'category_id.exists' =>
        "La catégorie sélectionnée n'existe pas.",

    'nom.required' =>
        'Le nom est obligatoire.',

    'nom.max' =>
        'Le nom ne doit pas dépasser 255 caractères.',

    'code.required' =>
        'Le code est obligatoire.',

    'code.max' =>
        'Le code ne doit pas dépasser 100 caractères.',

    'marque.required' =>
        'La marque est obligatoire.',

    'modele.required' =>
        'Le modèle est obligatoire.',

    'numero_serie.max' =>
        'Le numéro de série ne doit pas dépasser 255 caractères.',

    'usage_type.required' =>
        "Le type d'utilisation est obligatoire.",

    'usage_type.in' =>
        "Le type d'utilisation est invalide.",

    'etat.required' =>
        "L'état est obligatoire.",

    'storage_room_id.exists' =>
        "La salle de stockage sélectionnée n'existe pas.",

    'assigned_to.exists' =>
        "L'utilisateur sélectionné n'existe pas.",

];
    }

    /**
     * Nom lisible des attributs.
     */
    public function attributes(): array
    {
        return [

    'category_id' => 'catégorie',

    'nom' => 'nom',

    'code' => 'code',

    'marque' => 'marque',

    'modele' => 'modèle',

    'numero_serie' => 'numéro de série',

    'usage_type' => "type d'utilisation",

    'etat' => 'état',

    'storage_room_id' => 'salle de stockage',

    'localisation' => 'localisation',

    'assigned_to' => 'utilisateur',

];
    }
}
