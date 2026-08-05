<?php

use App\Enums\Ticket\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('entreprise_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('equipment_id')
                ->constrained('equipments');

            $table->foreignId('user_id')
                ->constrained('users');

            /*
            |--------------------------------------------------------------------------
            | Informations
            |--------------------------------------------------------------------------
            */

            $table->text('description');

            /*
            |--------------------------------------------------------------------------
            | Métier
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'statut',
                TicketStatus::values()
            )->default(
                TicketStatus::OUVERT->value
            );

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('entreprise_id');

            $table->index('equipment_id');

            $table->index('user_id');

            $table->index('statut');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'tickets'
        );
    }
};
