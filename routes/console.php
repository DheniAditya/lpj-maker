<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadikan / cabut admin: php artisan lpj:admin email@contoh.com [--remove]
Artisan::command('lpj:admin {email} {--remove}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error("Pengguna dengan email {$email} tidak ditemukan. Daftar dulu lewat halaman Register.");

        return 1;
    }

    $user->forceFill(['is_admin' => ! $this->option('remove')])->save();
    $this->info($user->is_admin ? "{$user->name} sekarang admin." : "Status admin {$user->name} dicabut.");

    return 0;
})->purpose('Jadikan atau cabut admin LPJ Maker');
