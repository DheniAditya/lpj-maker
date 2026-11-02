<?php

use Illuminate\Support\Facades\DB;

test('migrasi mengisi username akun lama dari email lamanya', function () {
    // Simulasikan akun lama: username kosong, email terisi
    DB::table('users')->insert([
        'name' => 'Lama', 'email' => 'Deni@IDN.sch.id', 'username' => null,
        'password' => bcrypt('password'), 'created_at' => now(), 'updated_at' => now(),
    ]);

    $migration = require database_path('migrations/2026_11_02_000001_add_username_to_users_table.php');
    $migration->up();

    expect(DB::table('users')->where('name', 'Lama')->value('username'))->toBe('deni@idn.sch.id');

    $this->post('/login', ['username' => 'deni@idn.sch.id', 'password' => 'password'])->assertRedirect(route('dashboard'));
});
