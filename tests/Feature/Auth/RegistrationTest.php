<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'langue' => 'fr',
    ]);

    // Vérifier que l'utilisateur a bien été créé en base
    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();

    // Vérifier que l'utilisateur est bien authentifié
    expect(auth()->check())->toBeTrue();

    // Le contrôleur redirige vers le dashboard (email vérifié automatiquement)
    $response->assertRedirect(route('dashboard', absolute: false));
});
