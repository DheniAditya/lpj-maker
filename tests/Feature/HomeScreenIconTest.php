<?php

test('halaman memuat ikon layar utama HP dan manifest', function () {
    $this->get(route('lpj.create'))
        ->assertOk()
        ->assertSee('rel="apple-touch-icon"', false)
        ->assertSee('rel="manifest"', false)
        ->assertSee('apple-mobile-web-app-title', false);

    foreach (['apple-touch-icon.png', 'favicon.ico', 'site.webmanifest', 'icons/icon-192.png', 'icons/icon-512.png', 'icons/icon-maskable-512.png'] as $file) {
        expect(public_path($file))->toBeFile();
    }

    $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true);
    expect($manifest['name'])->toBe('LPJ Maker')->and($manifest['icons'])->toHaveCount(3);
    expect(getimagesize(public_path('apple-touch-icon.png'))[0])->toBe(180);
});
