<?php

use App\Models\PasswordResetRequest;
use App\Models\User;

test('halaman lupa password tidak menyebut email', function () {
    $this->get('/forgot-password')->assertOk()->assertSee('Kirim permintaan ke admin')->assertDontSee('email');
});

test('pengguna mengirim permintaan lupa password, admin mendapat notifikasi', function () {
    $user = User::factory()->create(['name' => 'Ust Zidan', 'username' => 'zidan']);
    $admin = User::factory()->create(['is_admin' => true]);

    $this->post('/forgot-password', ['username' => 'Zidan', 'contact' => '0812-3456', 'note' => 'Unit SD'])
        ->assertSessionHas('status');

    $request = PasswordResetRequest::sole();
    expect($request->user_id)->toBe($user->id)->and($request->contact)->toBe('0812-3456')->and($request->resolved_at)->toBeNull();

    // badge di navbar admin
    $this->actingAs($admin)->get(route('dashboard'))->assertSee('Ada permintaan reset password');
    // daftar di panel admin
    $this->actingAs($admin)->get(route('admin.users.index'))
        ->assertSee('1 permintaan reset password')->assertSee('Ust Zidan')->assertSee('0812-3456')->assertSee('Unit SD');
    // pengguna biasa tidak melihat notifikasi
    $this->actingAs($user)->get(route('dashboard'))->assertDontSee('Ada permintaan reset password');
});

test('kirim ulang tidak menumpuk permintaan; username tak dikenal tetap dapat pesan sama', function () {
    User::factory()->create(['username' => 'zidan']);

    $this->post('/forgot-password', ['username' => 'zidan']);
    $this->post('/forgot-password', ['username' => 'zidan', 'note' => 'Tolong ya']);
    expect(PasswordResetRequest::pending()->where('username', 'zidan')->count())->toBe(1)
        ->and(PasswordResetRequest::first()->note)->toBe('Tolong ya');

    $this->post('/forgot-password', ['username' => 'tidak-ada'])->assertSessionHas('status');
    expect(PasswordResetRequest::where('username', 'tidak-ada')->first()->user_id)->toBeNull();
});

test('setelah admin mengganti password, permintaan otomatis selesai', function () {
    $user = User::factory()->create(['username' => 'zidan']);
    $admin = User::factory()->create(['is_admin' => true]);
    $this->post('/forgot-password', ['username' => 'zidan']);

    $this->actingAs($admin)->put(route('admin.users.password', $user), [
        'password' => 'PasswordBaru123', 'password_confirmation' => 'PasswordBaru123',
    ])->assertSessionHas('status');

    $request = PasswordResetRequest::sole();
    expect($request->resolved_at)->not->toBeNull()->and($request->resolved_by)->toBe($admin->id);

    auth()->logout();
    $this->post('/login', ['username' => 'zidan', 'password' => 'PasswordBaru123'])->assertRedirect(route('dashboard'));
});

test('admin bisa menandai permintaan selesai; non-admin tidak', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->post('/forgot-password', ['username' => 'tidak-ada']);
    $request = PasswordResetRequest::sole();

    $this->actingAs(User::factory()->create())->post(route('admin.reset-requests.resolve', $request))->assertForbidden();
    $this->actingAs($admin)->post(route('admin.reset-requests.resolve', $request))->assertSessionHas('status');

    expect($request->fresh()->resolved_at)->not->toBeNull();
});
