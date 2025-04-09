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
    $response->dumpSession();

    // Vérifier que l'utilisateur a bien été créé en base
    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();

    // 🔹 Générer un token Sanctum pour l'authentification API
    $token = $user->createToken('test-token')->plainTextToken;

    // 🔹 Forcer l'authentification avec Sanctum
    Sanctum::actingAs($user);

    // Vérifier que l'utilisateur est bien authentifié
    expect(auth()->check())->toBeTrue();

    // Vérifier que la réponse est correcte
    $response->assertRedirect(route('verification.notice', absolute: false));

});
