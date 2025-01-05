<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
     {
             //Empty the table first
             DB::statement('SET FOREIGN_KEY_CHECKS=0');
             User::truncate();
             DB::statement('SET FOREIGN_KEY_CHECKS=1');

             //Define data
             $users = [
            [
               'login'=>'bob',
               'firstname'=>'Bob',
               'lastname'=>'Sull',
               'email'=>'bob@sull.com',
               'password'=>'12345678',
               'remember_token' => Str::random(10),
               'langue'=>'fr',
               'created_at'=>'',
               'role'=>'admin',
             ],
             [
               'login'=>'anna',
               'firstname'=>'Anna',
               'lastname'=>'Lyse',
               'email'=>'anna.lyse@sull.com',
               'password'=>'12345678',
               'langue'=>'en',
               'created_at'=>'',
               'role'=>'member',
             ],
           ];

           foreach($users as &$user) {
             $user['email_verified_at'] = Carbon::now()->toDateTimeString();    //date('Y-m-d G:i:s');
             $user['created_at'] = Carbon::now()->toDateTimeString();    //date('Y-m-d G:i:s');
             $user['password'] = Hash::make($user['password']);
             $user['remember_token'] = Str::random(10);
           }
            //Insert data in the table
            DB::table('users')->insert($users);

            //Generate 1 admin
            User::factory()->create([
                'login' => 'fred',
                'firstname' => 'Fred',
                'lastname' => 'Sull',
                'email' => 'fred@sull.com',
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
                'langue' => 'fr',
                'role' => 'admin',
            ]); // Le crochet fermant est bien ici

            // Générer 20 membres
            User::factory(20)->create([
                'role' => 'member',
            ]);

            // Générer 5 critiques de presse
            User::factory(5)->create([
                'role' => 'press',
            ]);

            // Générer 3 sites affiliés
            User::factory(3)->create([
                'role' => 'affiliate',
            ]);
          }
     }
