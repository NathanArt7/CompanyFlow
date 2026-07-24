<?php

use App\Enums\Reservation\CancellationReason;
use App\Enums\Reservation\ReservationStatus;
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
        Schema::create('reservations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('entreprise_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('room_id')
                ->constrained('rooms');

            $table->foreignId('user_id')
                ->constrained('users');

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informations
            |--------------------------------------------------------------------------
            */

            $table->string('motif');

            $table->unsignedInteger(
                'nombre_participants'
            );

            $table->date('date_reservation');

            $table->time('heure_debut');

            $table->time('heure_fin');

            /*
            |--------------------------------------------------------------------------
            | Métier
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'statut',
                ReservationStatus::values()
            )->default(
                ReservationStatus::CONFIRMEE->value
            );

            $table->enum(
                'cancellation_reason',
                CancellationReason::values()
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('entreprise_id');

            $table->index('room_id');

            $table->index('user_id');

            $table->index('date_reservation');

            $table->index('statut');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'reservations'
        );
    }
};