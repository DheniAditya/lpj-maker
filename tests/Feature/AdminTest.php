<?php

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->member = User::factory()->create(['name' => 'Ust Aqil', 'email' => 'aqil@idn.sch.id']);
});

test('pengguna biasa dan tamu tidak bisa membuka panel admin', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    $this->actingAs($this->member)->get(route('admin.users.index'))->assertForbidden();
});

test('admin melihat daftar pengguna, email, dan bisa mencari', function () {
    $this->actingAs($this->admin)->get(route('admin.users.index'))
        ->assertOk()->assertSee('Ust Aqil')->assertSee('aqil@idn.sch.id');

    $this->actingAs($this->admin)->get(route('admin.users.index', ['q' => 'tidak-ada']))
        ->assertOk()->assertDontSee('aqil@idn.sch.id');
});

test('admin bisa membuka LPJ milik pengguna dan mengunduh PDF-nya', function () {
    $report = ExpenseReport::create(['title' => 'LPJ Aqil', 'creator_name' => 'Aqil', 'user_id' => $this->member->id]);

    $this->actingAs($this->admin)->get(route('admin.users.show', $this->member))->assertOk()->assertSee('LPJ Aqil');
    $this->actingAs($this->admin)->get(route('lpj.show', $report->slug))->assertOk()->assertSee('LPJ Aqil');
});

test('admin tidak bisa mengatur share LPJ milik orang lain', function () {
    $report = ExpenseReport::create(['title' => 'X', 'creator_name' => 'Y', 'user_id' => $this->member->id]);

    $this->actingAs($this->admin)->postJson(route('lpj.share.update', $report), ['is_shared' => 1])->assertForbidden();
});

test('password tidak pernah ditampilkan; admin bisa membuatkan password baru', function () {
    $this->actingAs($this->admin)->get(route('admin.users.show', $this->member))
        ->assertOk()->assertDontSee($this->member->password);

    $this->actingAs($this->admin)->put(route('admin.users.password', $this->member), [
        'password' => 'PasswordBaru123',
        'password_confirmation' => 'PasswordBaru123',
    ])->assertSessionHas('status');

    expect(Hash::check('PasswordBaru123', $this->member->fresh()->password))->toBeTrue();
});

test('admin bisa mengirim link reset password ke email pengguna', function () {
    Notification::fake();

    $this->actingAs($this->admin)->post(route('admin.users.reset-link', $this->member))->assertSessionHas('status');

    Notification::assertSentTo($this->member, ResetPassword::class);
});

test('admin bisa mengubah data dan menjadikan admin', function () {
    $this->actingAs($this->admin)->patch(route('admin.users.update', $this->member), [
        'name' => 'Aqil Baru', 'email' => 'aqil.baru@idn.sch.id', 'is_admin' => '1',
    ])->assertSessionHas('status');

    $member = $this->member->fresh();
    expect($member->name)->toBe('Aqil Baru')->and($member->isAdmin())->toBeTrue()->and($member->email_verified_at)->toBeNull();
});

test('admin tidak bisa mencabut admin atau menghapus akun sendiri', function () {
    $this->actingAs($this->admin)->patch(route('admin.users.update', $this->admin), [
        'name' => $this->admin->name, 'email' => $this->admin->email, 'is_admin' => '0',
    ])->assertSessionHasErrors('is_admin');

    $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin))->assertSessionHasErrors('user');
    expect($this->admin->fresh())->not->toBeNull()->and($this->admin->fresh()->isAdmin())->toBeTrue();
});

test('admin menghapus pengguna beserta LPJ dan file notanya', function () {
    $report = ExpenseReport::create(['title' => 'X', 'creator_name' => 'Y', 'user_id' => $this->member->id]);
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'Nota', 'amount' => 1]);
    $entry->attachImages([UploadedFile::fake()->image('n.jpg')]);
    $path = $entry->images()->sole()->image_path;

    $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->member))
        ->assertRedirect(route('admin.users.index'));

    expect(User::find($this->member->id))->toBeNull()->and(ExpenseReport::count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});

test('perintah lpj:admin menjadikan dan mencabut admin', function () {
    $this->artisan('lpj:admin', ['email' => 'aqil@idn.sch.id'])->assertSuccessful();
    expect($this->member->fresh()->isAdmin())->toBeTrue();

    $this->artisan('lpj:admin', ['email' => 'aqil@idn.sch.id', '--remove' => true])->assertSuccessful();
    expect($this->member->fresh()->isAdmin())->toBeFalse();

    $this->artisan('lpj:admin', ['email' => 'tidak@ada.id'])->assertFailed();
});
