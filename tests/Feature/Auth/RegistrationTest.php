<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'username' => 'test.user',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('pendaftaran ditolak jika username sudah dipakai', function () {
    \App\Models\User::factory()->create(['username' => 'deni']);

    $this->post('/register', [
        'name' => 'Lain', 'username' => 'DENI', 'password' => 'password', 'password_confirmation' => 'password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('halaman daftar memakai username dan pesan error berbahasa Indonesia', function () {
    $this->get('/register')->assertOk()->assertSee('Buat akun baru')->assertSee('name="username"', false);

    $this->post('/register', ['name' => 'Deni', 'username' => 'deni', 'password' => 'abc', 'password_confirmation' => 'abd'])
        ->assertSessionHasErrors(['password' => 'Ulangi password tidak sama.']);
});
