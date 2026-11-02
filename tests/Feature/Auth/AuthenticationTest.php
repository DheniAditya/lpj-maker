<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('login tidak peka huruf besar pada username', function () {
    $user = \App\Models\User::factory()->create(['username' => 'deni.aditya']);

    $this->post('/login', ['username' => 'Deni.Aditya', 'password' => 'password'])->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('Ingat saya: logout di satu perangkat tidak mengeluarkan perangkat lain', function () {
    $user = \App\Models\User::factory()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password', 'remember' => 'on']);
    $token = $user->fresh()->remember_token;
    expect($token)->not->toBeNull();

    $this->post('/logout');

    // remember token tidak diganti, jadi cookie "Ingat saya" di perangkat lain tetap berlaku
    expect($user->fresh()->remember_token)->toBe($token);
});

test('halaman login berbahasa Indonesia, memakai username, dan Ingat saya tercentang', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('name="username"', false)
        ->assertSee('Ingat saya')
        ->assertDontSee('type="email"', false)
        ->assertSee('name="remember" checked', false);
});
