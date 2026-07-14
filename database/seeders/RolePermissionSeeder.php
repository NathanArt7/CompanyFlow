<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // Récupération des rôles
        // ─────────────────────────────────────────────

        $superAdmin = Role::where(
            'nom',
            RoleName::SUPER_ADMIN->value
        )->firstOrFail();

        $admin = Role::where(
            'nom',
            RoleName::ADMIN->value
        )->firstOrFail();

        $technicien = Role::where(
            'nom',
            RoleName::TECHNICIEN->value
        )->firstOrFail();

        $superEmploye = Role::where(
            'nom',
            RoleName::SUPER_EMPLOYE->value
        )->firstOrFail();

        $employe = Role::where(
            'nom',
            RoleName::EMPLOYE->value
        )->firstOrFail();

        // ─────────────────────────────────────────────
        // Récupération des permissions
        // ─────────────────────────────────────────────

        $permissions = Permission::all()->keyBy('nom');

        // Nettoyage des anciennes associations
        Role::query()->each(function ($role) {
            $role->permissions()->detach();
        });

        // ─────────────────────────────────────────────
        // Super Administrateur
        // Toutes les permissions métier
        // ─────────────────────────────────────────────

        $superAdmin->permissions()->attach(
            $permissions
                ->except([
                    'creer_entreprise',
                    'modifier_entreprise',
                    'desactiver_entreprise',
                    'consulter_entreprises',
                    'creer_super_admin',
                ])
                ->pluck('id')
                ->toArray()
        );

        // ─────────────────────────────────────────────
        // Administrateur
        // ─────────────────────────────────────────────

        $admin->permissions()->attach([
            $permissions['creer_technicien']->id,
            $permissions['creer_employe']->id,
            $permissions['creer_super_employe']->id,
            $permissions['modifier_utilisateur']->id,
            $permissions['desactiver_compte']->id,

            $permissions['consulter_salle']->id,
            $permissions['creer_salle']->id,
            $permissions['modifier_salle']->id,
            $permissions['supprimer_salle']->id,
            $permissions['bloquer_salle']->id,

            $permissions['ajouter_materiel']->id,
            $permissions['modifier_materiel']->id,
            $permissions['supprimer_materiel']->id,
            $permissions['modifier_etat_materiel']->id,

            $permissions['consulter_toutes_reservations']->id,
            $permissions['valider_reservation']->id,
            $permissions['annuler_reservation']->id,

            $permissions['consulter_tous_tickets']->id,
            $permissions['attribuer_ticket']->id,
            $permissions['modifier_priorite_ticket']->id,

            $permissions['modifier_son_profil']->id,
            $permissions['recevoir_notifications']->id,
        ]);

        // ─────────────────────────────────────────────
        // Technicien
        // ─────────────────────────────────────────────

        $technicien->permissions()->attach([
            $permissions['consulter_tous_tickets']->id,
            $permissions['accepter_ticket']->id,
            $permissions['changer_statut_ticket']->id,
            $permissions['commenter_ticket']->id,
            $permissions['modifier_etat_materiel']->id,
            $permissions['cloturer_ticket']->id,

            $permissions['modifier_son_profil']->id,
            $permissions['recevoir_notifications']->id,
        ]);

        // ─────────────────────────────────────────────
        // Super Employé
        // ─────────────────────────────────────────────

        $superEmploye->permissions()->attach([
            $permissions['consulter_salle']->id,
            $permissions['reserver_salle']->id,
            $permissions['consulter_disponibilites']->id,
            $permissions['emprunter_materiel']->id,
            $permissions['consulter_ses_reservations']->id,

            $permissions['creer_ticket']->id,
            $permissions['consulter_ses_tickets']->id,
            $permissions['commenter_ticket']->id,

            $permissions['modifier_son_profil']->id,
            $permissions['recevoir_notifications']->id,
        ]);

        // ─────────────────────────────────────────────
        // Employé
        // ─────────────────────────────────────────────

        $employe->permissions()->attach([
            $permissions['consulter_salle']->id,
            $permissions['consulter_disponibilites']->id,

            $permissions['creer_ticket']->id,
            $permissions['consulter_ses_tickets']->id,
            $permissions['commenter_ticket']->id,

            $permissions['modifier_son_profil']->id,
            $permissions['recevoir_notifications']->id,
        ]);
    }
}