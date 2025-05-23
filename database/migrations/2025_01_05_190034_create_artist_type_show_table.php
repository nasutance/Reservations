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
        Schema::create('artist_type_show', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_type_id')->constrained('artist_type')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('show_id')->constrained('shows')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_type_show');
    }
};
