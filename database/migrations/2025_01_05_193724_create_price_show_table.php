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
        Schema::create('price_show', function (Blueprint $table) {
            $table->foreignId('show_id');
            $table->foreignId('price_id');

            $table->foreign('show_id')
                ->references('id')
                ->on('shows')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('price_id')
                ->references('id')
                ->on('prices')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_show');
    }
};
