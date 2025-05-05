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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id(); // Colonne auto-incrémentée pour l'identifiant unique.
            $table->foreignId('user_id'); // Colonne pour stocker l'ID d'un utilisateur, clé étrangère.
            $table->string('status', 60); // Colonne pour stocker le statut de la réservation.
            $table->timestamp('booking_date')->useCurrent(); // Colonne pour la date de réservation.
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate(); // Colonne pour la date de mise à jour.
            $table->softDeletes(); // Ajout de la suppression logique (deleted_at).

            // Définir la relation avec la table 'users'.
            $table->foreign('user_id')
                  ->references('id')->on('users') // Relie 'user_id' à la colonne 'id' de 'users'.
                  ->onDelete('restrict') // Interdit la suppression d'un utilisateur si des réservations existent.
                  ->onUpdate('cascade'); // Met à jour automatiquement 'user_id' si l'ID utilisateur change.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations'); // Supprime la table 'reservations' si elle existe.
    }
};
