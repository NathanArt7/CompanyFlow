<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('rooms', function (Blueprint $table) {

        $table->id();

        $table->string('nom');

        $table->string('code')->unique();

        $table->unsignedInteger('capacite');

        $table->string('localisation');

        $table->text('description')->nullable();

        $table->enum('statut', [
            'Disponible',
            'Occupée',
            'En maintenance',
            'Hors service'
        ])->default('Disponible');

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
