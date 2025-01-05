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
        Schema::create('representations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable();
            $table->foreignId('show_id');
            $table->datetime('schedule');

            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('show_id')
                ->references('id')
                ->on('shows')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representations');
    }
};
