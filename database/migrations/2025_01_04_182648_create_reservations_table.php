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
      // Crée une table nommée 'reservations' dans la base de données.
      Schema::create('reservations', function (Blueprint $table) {
          $table->id(); // Colonne auto-incrémentée pour l'identifiant unique.
          $table->foreignId('user_id'); // Colonne pour stocker l'ID d'un utilisateur, clé étrangère.
          $table->string('status', 60); // Colonne pour stocker le statut de la réservation, max 60 caractères.
          $table->timestamp('booking_date')->useCurrent(); // Colonne pour la date de réservation avec la valeur actuelle par défaut.
          $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate(); // Colonne pour la date de mise à jour, mise à jour automatique si modifiée.

          // Définit une clé étrangère pour 'user_id' pointant vers la table 'users'.
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
        Schema::dropIfExists('reservations');
    }
};
