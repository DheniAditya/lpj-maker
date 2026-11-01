<?php

use App\Models\ExpenseReport;
use App\Models\User;

test('akun biasa maksimal 15 arsip LPJ', function () {
    $user = User::factory()->create();
    foreach (range(1, User::MAX_REPORTS) as $i) {
        ExpenseReport::create(['title' => "LPJ $i", 'creator_name' => 'Deni', 'user_id' => $user->id]);
    }

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('lpj.store'), ['title' => 'LPJ 16', 'creator_name' => 'Deni'])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasErrors('title');

    expect($user->expenseReports()->count())->toBe(User::MAX_REPORTS);

    $this->actingAs($user)->get(route('dashboard'))->assertSee('Arsip sudah penuh');
});

test('admin tidak dibatasi jumlah arsip', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    foreach (range(1, User::MAX_REPORTS) as $i) {
        ExpenseReport::create(['title' => "LPJ $i", 'creator_name' => 'Admin', 'user_id' => $admin->id]);
    }

    $this->actingAs($admin)->post(route('lpj.store'), ['title' => 'LPJ 16', 'creator_name' => 'Admin']);

    expect($admin->expenseReports()->count())->toBe(User::MAX_REPORTS + 1);
});

test('membatalkan link share mematikan link lama untuk semua penerima', function () {
    $owner = User::factory()->create();
    $report = ExpenseReport::create(['title' => 'X', 'creator_name' => 'Y', 'user_id' => $owner->id, 'is_shared' => true, 'share_role' => 'editor']);
    $oldToken = $report->share_token;

    // penerima membuka link
    $this->get(route('lpj.share', $oldToken));
    $this->get(route('lpj.show', $report->slug))->assertOk();

    // pemilik membatalkan
    $this->actingAs($owner)->postJson(route('lpj.share.revoke', $report))
        ->assertOk()->assertJsonPath('success', true);
    auth()->logout();

    $report->refresh();
    expect($report->is_shared)->toBeFalse()->and($report->share_token)->not->toBe($oldToken);

    $this->get(route('lpj.show', $report->slug))->assertForbidden();
    $this->get(route('lpj.share', $oldToken))->assertNotFound();

    // dibagikan lagi: link lama tetap mati
    $this->actingAs($owner)->postJson(route('lpj.share.update', $report), ['is_shared' => 1, 'share_role' => 'viewer'])->assertOk();
    auth()->logout();
    $this->get(route('lpj.share', $oldToken))->assertNotFound();
    $this->get(route('lpj.share', $report->fresh()->share_token))->assertRedirect();
});

test('hanya pemilik yang bisa membatalkan link share', function () {
    $report = ExpenseReport::create(['title' => 'X', 'creator_name' => 'Y', 'user_id' => User::factory()->create()->id, 'is_shared' => true]);

    $this->actingAs(User::factory()->create())->postJson(route('lpj.share.revoke', $report))->assertForbidden();
});

test('peran share tidak valid ditolak', function () {
    $owner = User::factory()->create();
    $report = ExpenseReport::create(['title' => 'X', 'creator_name' => 'Y', 'user_id' => $owner->id]);

    $this->actingAs($owner)->postJson(route('lpj.share.update', $report), ['is_shared' => 1, 'share_role' => 'owner'])
        ->assertUnprocessable();
});
