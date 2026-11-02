<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('nama dan username bisa diubah di profil (username jadi huruf kecil)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'Test User', 'username' => 'Deni.Baru'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->name)->toBe('Test User')->and($user->username)->toBe('deni.baru');
});

test('username harus unik dan tanpa spasi', function () {
    User::factory()->create(['username' => 'dipakai']);
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', ['name' => 'X', 'username' => 'dipakai'])->assertSessionHasErrors('username');
    $this->actingAs($user)->patch('/profile', ['name' => 'X', 'username' => 'ada spasi'])->assertSessionHasErrors('username');
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
