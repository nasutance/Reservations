<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Models\Reservation;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{

    public function register(){}

    public function boot(){

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

      Gate::define('view-all-reservations', function (User $user) {
       return $user->roles()->where('role', 'admin')->exists();
     });

      Gate::define('view-shows', function (User $user) {
        return $user->roles->contains('role', 'member') ||
               $user->roles->contains('role', 'admin') ||
               $user->roles->contains('role', 'press');
             });

      // Autoriser un utilisateur à modifier sa propre réservation
      Gate::define('update-reservation', function (User $user, Reservation $reservation) {
        return $user->id === $reservation->user_id || $user->roles()->where('role', 'admin')->exists();
      });

      // Autoriser un utilisateur à supprimer sa propre réservation
      Gate::define('delete-reservation', function (User $user, Reservation $reservation) {
        return $user->id === $reservation->user_id || $user->roles()->where('role', 'admin')->exists();
      });

      // Charger les routes API
      Route::middleware('api')
      ->prefix('api')
      ->group(base_path('routes/api.php'));
    }
}
