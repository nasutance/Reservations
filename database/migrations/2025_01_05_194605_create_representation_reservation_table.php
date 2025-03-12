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
        Schema::create('representation_reservation', function (Blueprint $table) {
            $table->foreignId('representation_id');
            $table->foreignId('reservation_id');

            $table->tinyInteger('quantity')->default(1);

            $table->foreignId('price_id')
                ->constrained('prices')
                ->onDelete('cascade');

            $table->foreign('representation_id')
                ->references('id')
                ->on('representations')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representation_reservation');
    }
};
