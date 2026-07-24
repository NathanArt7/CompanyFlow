<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        Schema::create('account_activations', function (Blueprint $table) {

            $table->id();

            // Utilisateur dont le compte doit être activé
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Utilisateur qui a créé ce compte (Admin ou Super Admin)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            // Token d'activation
            $table->string('token')->unique();

            // Date d'expiration du lien
            $table->timestamp('expires_at')->index();

            // Date d'utilisation du lien (null = jamais utilisé)
            $table->timestamp('used_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_activations');
    }
};