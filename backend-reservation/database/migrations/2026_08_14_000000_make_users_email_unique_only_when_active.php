<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * L'email ne doit rester unique que parmi les comptes actifs (deleted_at IS
     * NULL). Sinon, un compte supprimé (soft delete, cf.
     * add_deleted_at_to_users_table) garde son email pour toujours dans l'index
     * unique classique et bloque définitivement toute réutilisation de cette
     * adresse — typiquement : lien d'activation expiré, suppression du compte
     * pour ré-inviter la personne, puis erreur "cette adresse email est déjà
     * utilisée" alors que le compte n'apparaît plus nulle part.
     */
    public function up(): void
    {
        Schema::table('users', function ($table) {
            $table->dropUnique('users_email_unique');
        });

        DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS users_email_unique');

        Schema::table('users', function ($table) {
            $table->unique('email');
        });
    }
};
