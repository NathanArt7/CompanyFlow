<?php

use App\Enums\EntrepriseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprises', function (Blueprint $table) {

            $table->id();

            $table->string('reference')
            ->nullable()
            ->unique();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nom')->nullable();

            $table->string('email')->nullable();

            $table->string('telephone')->nullable();

            $table->string('adresse')->nullable();

            $table->string('logo')->nullable();

            $table->enum('statut', [
                EntrepriseStatus::EN_ATTENTE->value,
                EntrepriseStatus::ACTIVE->value,
                EntrepriseStatus::SUSPENDUE->value,
            ])->default(EntrepriseStatus::EN_ATTENTE->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};