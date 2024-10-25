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
            //$table->string('name', 60)->change();	//Ne fonctionne pas avec ENUM
            $table->renameColumn('name', 'firstname');
            $table->string('lastname', 60)->after('firstname');
            $table->string('login', 30)->after('id');
            $table->string('langue', 2);
            $table->enum('role', ['admin','member','affiliate','press',])
                ->default('member');
            $table->unique('login', 'users_login_unique');
        });

        DB::statement('ALTER TABLE users MODIFY COLUMN firstname VARCHAR(60)');
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_login_unique');
            $table->dropColumn(['role', 'langue', 'login', 'lastname']);
            $table->string('firstname', 255)->change();//fonctionne
            $table->renameColumn('firstname', 'name');
        });
    }
};
