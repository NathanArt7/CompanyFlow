<?php

namespace App\Enums;

enum PermissionName: string
{
    // ───────────────────────────────
    // Plateforme
    // ───────────────────────────────

    case CREER_ENTREPRISE = 'creer_entreprise';
    case MODIFIER_ENTREPRISE = 'modifier_entreprise';
    case DESACTIVER_ENTREPRISE = 'desactiver_entreprise';
    case CONSULTER_ENTREPRISES = 'consulter_entreprises';
    case CREER_SUPER_ADMIN = 'creer_super_admin';

    // ───────────────────────────────
    // Utilisateurs
    // ───────────────────────────────

    case CREER_ADMIN = 'creer_admin';
    case MODIFIER_ADMIN = 'modifier_admin';
    case SUPPRIMER_ADMIN = 'supprimer_admin';

    case CREER_TECHNICIEN = 'creer_technicien';
    case CREER_EMPLOYE = 'creer_employe';
    case CREER_SUPER_EMPLOYE = 'creer_super_employe';

    case MODIFIER_UTILISATEUR = 'modifier_utilisateur';
    case DESACTIVER_COMPTE = 'desactiver_compte';
    case REINITIALISER_MOT_DE_PASSE = 'reinitialiser_mot_de_passe';

    case ATTRIBUER_ROLE = 'attribuer_role';

    // ───────────────────────────────
    // Salles
    // ───────────────────────────────

    case CREER_SALLE = 'creer_salle';
    case MODIFIER_SALLE = 'modifier_salle';
    case SUPPRIMER_SALLE = 'supprimer_salle';
    case BLOQUER_SALLE = 'bloquer_salle';

    // ───────────────────────────────
    // Matériels
    // ───────────────────────────────

    case AJOUTER_MATERIEL = 'ajouter_materiel';
    case MODIFIER_MATERIEL = 'modifier_materiel';
    case SUPPRIMER_MATERIEL = 'supprimer_materiel';
    case MODIFIER_ETAT_MATERIEL = 'modifier_etat_materiel';

    // ───────────────────────────────
    // Réservations
    // ───────────────────────────────

    case RESERVER_SALLE = 'reserver_salle';
    case CONSULTER_SES_RESERVATIONS = 'consulter_ses_reservations';
    case CONSULTER_TOUTES_RESERVATIONS = 'consulter_toutes_reservations';
    case VALIDER_RESERVATION = 'valider_reservation';
    case ANNULER_RESERVATION = 'annuler_reservation';
    case CONSULTER_DISPONIBILITES = 'consulter_disponibilites';
    case EMPRUNTER_MATERIEL = 'emprunter_materiel';

    // ───────────────────────────────
    // Tickets
    // ───────────────────────────────

    case CREER_TICKET = 'creer_ticket';
    case CONSULTER_SES_TICKETS = 'consulter_ses_tickets';
    case CONSULTER_TOUS_TICKETS = 'consulter_tous_tickets';
    case ATTRIBUER_TICKET = 'attribuer_ticket';
    case MODIFIER_PRIORITE_TICKET = 'modifier_priorite_ticket';
    case ACCEPTER_TICKET = 'accepter_ticket';
    case CHANGER_STATUT_TICKET = 'changer_statut_ticket';
    case COMMENTER_TICKET = 'commenter_ticket';
    case CLOTURER_TICKET = 'cloturer_ticket';

    // ───────────────────────────────
    // Système
    // ───────────────────────────────

    case CONFIGURER_SYSTEME = 'configurer_systeme';
    case CONSULTER_JOURNAUX = 'consulter_journaux';
    case GERER_SAUVEGARDES = 'gerer_sauvegardes';
    case GERER_PERMISSIONS = 'gerer_permissions';
    case VOIR_DASHBOARD_GLOBAL = 'voir_dashboard_global';

    // ───────────────────────────────
    // Commun
    // ───────────────────────────────

    case MODIFIER_SON_PROFIL = 'modifier_son_profil';
    case RECEVOIR_NOTIFICATIONS = 'recevoir_notifications';
}