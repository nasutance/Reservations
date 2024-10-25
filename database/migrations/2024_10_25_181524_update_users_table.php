<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
 {
     Schema::table('users', function (Blueprint $table) {
         $table->string('login')->nullable()->change();  // Rendre le champ nullable
         // OU ajouter une valeur par défaut
         // $table->string('login')->default('default_login')->change();
     });
 }

 public function down()
 {
     Schema::table('users', function (Blueprint $table) {
         $table->string('login')->nullable(false)->change(); // Si tu veux revenir à la version précédente
     });
 }

};
