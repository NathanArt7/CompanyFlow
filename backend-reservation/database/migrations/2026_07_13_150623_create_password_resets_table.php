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
        Schema::create('password_resets', function (Blueprint $table) {

            $table->id();

            // Utilisateur concerné
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Token de réinitialisation
            $table->string('token')->unique();

            // Expiration du lien
            $table->timestamp('expires_at')->index();

            // Date d'utilisation
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};