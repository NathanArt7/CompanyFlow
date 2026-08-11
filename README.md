# CompanyPilot

CompanyPilot est une application web de gestion de réservations en entreprise (salles et matériel), multi-tenant : chaque entreprise dispose de son propre espace, de ses utilisateurs, de ses rôles/permissions et de ses ressources.

Le dépôt contient deux applications séparées :

- [`backend-reservation/`](backend-reservation) — API REST Laravel 12 (PHP 8.2, Sanctum)
- [`frontend-reservation/`](frontend-reservation) — Interface web Nuxt 4 / Vue 3

## Fonctionnalités

- **Multi-entreprise** : inscription d'une entreprise, activation de compte par e-mail, configuration propre à chaque organisation
- **Utilisateurs, rôles et permissions** : gestion des comptes, rôles personnalisables, permissions granulaires par action (ex. `creer_salle`, `configurer_systeme`, `accepter_ticket`…)
- **Salles** : création, disponibilité, statuts, statistiques
- **Matériel** : catégories de matériel, gestion des équipements, emprunt, disponibilité
- **Réservations** : création, consultation, annulation, vue par semaine/jour, rappels automatiques par cron
- **Tickets** : signalement, acceptation, clôture (ex. incidents sur salle/matériel)
- **Dashboard** : statistiques globales et alertes
- **Notifications** : préférences par utilisateur, notifications non lues, marquage comme lu
- **Journaux d'activité** : traçabilité des actions
- **E-mails transactionnels** via l'API HTTP de Brevo

## Stack technique

**Backend** (`backend-reservation/`)
- Laravel 12 / PHP 8.2
- Laravel Sanctum (authentification API par token)
- SQLite en local, PostgreSQL en production
- Symfony Mailer + transport Brevo (API HTTP)
- PHPUnit pour les tests

**Frontend** (`frontend-reservation/`)
- Nuxt 4 / Vue 3
- Pinia (state management)
- Tailwind CSS
- VueUse
- jsPDF / xlsx pour l'export de documents

## Prérequis

- PHP >= 8.2, Composer
- Node.js (LTS récent) et npm
- SQLite (par défaut en local) ou PostgreSQL

## Installation

### Backend

```bash
cd backend-reservation
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # si DB_CONNECTION=sqlite
php artisan migrate --seed
```

Lancer le serveur de développement (API + queue + logs) :

```bash
composer run dev
```

L'API est alors disponible sur `http://localhost:8000`.

### Frontend

```bash
cd frontend-reservation
npm install
npm run dev
```

L'application est alors disponible sur `http://localhost:3000` et interroge l'API sur `http://localhost:8000/api` (configurable via `runtimeConfig.public.apiBase` dans [`nuxt.config.ts`](frontend-reservation/nuxt.config.ts)).

## Variables d'environnement notables (backend)

| Variable | Description |
|---|---|
| `DB_CONNECTION` | `sqlite` en local, `pgsql` en production |
| `MAIL_MAILER` | Driver d'envoi d'e-mail (Brevo en production) |
| `CRON_SECRET` | Secret partagé pour sécuriser l'endpoint `/api/cron/reminders` (appelé par un cron externe, sans session utilisateur) |

## Déploiement

Le backend est packagé via [`backend-reservation/Dockerfile`](backend-reservation/Dockerfile) (image `php:8.2-cli`, migrations + seed exécutés au démarrage du conteneur) pour un déploiement type Render.

## Tests

```bash
cd backend-reservation
composer test
```
