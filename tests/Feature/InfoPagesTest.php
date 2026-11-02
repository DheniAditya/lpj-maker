<?php

use App\Models\User;

test('halaman Fitur, Cara Pakai, dan Bantuan bisa dibuka tanpa login', function (string $route, string $text) {
    $this->get(route($route))->assertOk()->assertSee($text)
        ->assertSee(route('login'))->assertSee(route('register'));
})->with([
    ['fitur', 'Semua yang dibutuhkan'],
    ['cara-pakai', '5 langkah dari nota'],
    ['bantuan', 'Ada kendala?'],
]);

test('Bantuan menautkan WhatsApp admin dan halaman lupa password', function () {
    config(['lpj.admin_whatsapp' => '6281234567890']);

    $this->get(route('bantuan'))->assertOk()
        ->assertSee('https://wa.me/6281234567890')
        ->assertSee(route('password.request'))
        ->assertSee('Chat admin di WhatsApp');
});

test('menu Fitur, Cara Pakai, Bantuan muncul di halaman tamu dan halaman aplikasi', function () {
    foreach ([route('lpj.create'), route('login'), route('register')] as $url) {
        $this->get($url)->assertOk()
            ->assertSee(route('fitur'))->assertSee(route('cara-pakai'))->assertSee(route('bantuan'));
    }

    $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk()
        ->assertSee(route('fitur'))->assertSee(route('cara-pakai'))->assertSee(route('bantuan'));
});

test('pengguna yang sudah login melihat tombol Dashboard di halaman informasi', function () {
    $this->actingAs(User::factory()->create())->get(route('fitur'))
        ->assertOk()->assertSee(url('/dashboard'))->assertDontSee('Join now');
});
