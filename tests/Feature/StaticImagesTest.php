<?php

/*
 * Gambar latar & logo harus berada di public/images (ikut tersimpan di git).
 * Folder public/build dikosongkan setiap kali `npm run build`, jadi gambar
 * yang ditaruh di sana akan hilang.
 */
test('gambar latar dan logo tersedia di public/images, bukan di public/build', function () {
    foreach (['kelas.jpg', 'icon_only_2.png'] as $file) {
        expect(public_path('images/'.$file))->toBeFile();
    }

    $views = collect(\Illuminate\Support\Facades\File::allFiles(resource_path('views')))
        ->filter(fn ($f) => str_contains($f->getContents(), "asset('build/"));

    expect($views->map->getRelativePathname()->values()->all())->toBe([]);
});
