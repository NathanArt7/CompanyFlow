<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Plateforme
            'modifier_entreprise',
            'desactiver_entreprise',
            'consulter_entreprises',
            
            // Utilisateurs
            'creer_admin',
            'modifier_admin',
            'supprimer_admin',
            'creer_technicien',
            'creer_employe',
            'creer_super_employe',
            'modifier_utilisateur',
            'desactiver_compte',
            'reinitialiser_mot_de_passe',
            'attribuer_role',

            // Salles
            'creer_salle',
            'modifier_salle',
            'supprimer_salle',
            'bloquer_salle',

            // Matériel
            'ajouter_materiel',
            'modifier_materiel',
            'supprimer_materiel',
            'modifier_etat_materiel',

            // Réservations
            'reserver_salle',
            'consulter_ses_reservations',
            'consulter_toutes_reservations',
            'valider_reservation',
            'annuler_reservation',
            'consulter_disponibilites',
            'emprunter_materiel',

            // Tickets
            'creer_ticket',
            'consulter_ses_tickets',
            'consulter_tous_tickets',
            'attribuer_ticket',
            'modifier_priorite_ticket',
            'accepter_ticket',
            'changer_statut_ticket',
            'commenter_ticket',
            'cloturer_ticket',

            // Système
            'configurer_systeme',
            'consulter_journaux',
            'gerer_sauvegardes',
            'gerer_permissions',
            'voir_dashboard_global',

            // Commun
            'modifier_son_profil',
            'recevoir_notifications',
        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'nom' => $permission
            ]);

        }
    }
}