<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Models\Reservation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{

    public function register(){}

    public function boot(){

      if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
      }

      Gate::define('index-artist', function (User $user) {
        return $user->role === 'admin' or $user->role === 'member';
      });

      Gate::define('show-artist', function (User $user) {
        return $user->role === 'admin' or $user->role === 'member';
      });

      Gate::define('create-artist', function (User $user) {
        return $user->role === 'admin';
      });

      Gate::define('update-artist', function (User $user) {
        return $user->role === 'admin';
      });

      Gate::define('delete-artist', function (User $user) {
        return $user->role === 'admin';
      });

      Gate::define('manage-shows', function (User $user) {
        return $user->roles()->where('role', 'admin')->exists();
      });

      Gate::define('manage-representations', function (User $user) {
        return $user->roles()->where('role', 'admin')->exists();
      });

          Gate::define('view-shows', function (User $user) {
        return $user->roles->contains('role', 'member') ||
               $user->roles->contains('role', 'admin') ||
               $user->roles->contains('role', 'press');
             });

      // Charger les routes API
      Route::middleware('api')
      ->prefix('api')
      ->group(base_path('routes/api.php'));
    }
}
