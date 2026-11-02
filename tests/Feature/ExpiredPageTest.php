<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

/** Paksa semua token CSRF dianggap kedaluwarsa (seperti halaman login yang dibuka terlalu lama). */
function expireCsrfTokens(): void
{
    app()->instance(ValidateCsrfToken::class, new class(app(), app('encrypter')) extends ValidateCsrfToken
    {
        protected function runningUnitTests()
        {
            return false;
        }

        protected function tokensMatch($request)
        {
            return false;
        }
    });
}

test('token kedaluwarsa saat login: kembali ke halaman login dengan pesan, bukan halaman 419', function () {
    expireCsrfTokens();

    $this->from('/login')
        ->post('/login', ['username' => 'deni', 'password' => 'rahasia', 'remember' => 'on'])
        ->assertRedirect('/login')
        ->assertSessionHas('status', 'Halaman sudah terlalu lama dibuka. Silakan coba lagi.')
        ->assertSessionHasInput('username', 'deni')
        ->assertSessionMissing('_old_input.password');
});

test('token kedaluwarsa tapi sudah login (Ingat saya): langsung ke dashboard', function () {
    expireCsrfTokens();

    $this->actingAs(User::factory()->create())
        ->from('/login')
        ->post('/login', ['username' => 'x', 'password' => 'y'])
        ->assertRedirect(route('dashboard'));
});

test('token kedaluwarsa saat logout: tetap keluar', function () {
    expireCsrfTokens();

    $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');
    $this->assertGuest();
});

test('halaman login tidak disimpan di cache browser', function () {
    $response = $this->get('/login');

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});
