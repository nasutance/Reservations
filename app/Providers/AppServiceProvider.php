<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{

    public function register(){}

    public function boot(){
          Gate::define('create-artist', function (User $user) {
            return $user->role === 'admin';});
          Gate::define('update-artist', function (User $user) {
            return $user->role === 'admin';
          });
          Gate::define('delete-artist', function (User $user) {
            return $user->role === 'admin';
          });
    }
}
