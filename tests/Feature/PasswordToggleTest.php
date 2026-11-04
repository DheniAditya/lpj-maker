<?php

use App\Models\User;

test('kolom password di halaman Masuk dan Daftar punya tombol lihat password', function (string $route) {
    $this->get(route($route))->assertOk()
        ->assertSee('Lihat password')
        ->assertSee("show ? 'text' : 'password'", false);
})->with(['login', 'register']);

test('kolom password di Edit Akun punya tombol lihat password', function () {
    $html = $this->actingAs(User::factory()->create())->get(route('profile.edit'))->assertOk()->getContent();

    // 3 kolom ganti password + 1 kolom konfirmasi hapus akun
    expect(substr_count($html, "x-bind:type=\"show ? 'text' : 'password'\""))->toBe(4);
});
