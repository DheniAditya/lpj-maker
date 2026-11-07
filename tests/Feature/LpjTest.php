<?php

use App\Models\ExpenseEntry;
use App\Models\ExpenseReport;
use App\Models\User;
use App\Policies\ExpenseReportPolicy;
use App\Support\SignatureImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

function makeReport(array $attributes = []): ExpenseReport
{
    return ExpenseReport::create($attributes + ['title' => 'Lomba Robotik', 'creator_name' => 'Deni']);
}

/** Gambar tanda tangan palsu: garis gelap di atas latar putih (atau transparan). */
function signatureImage(bool $transparent = false, string $format = 'png'): string
{
    $image = imagecreatetruecolor(400, 200);
    imagesavealpha($image, true);
    imagealphablending($image, false);
    imagefill($image, 0, 0, $transparent ? imagecolorallocatealpha($image, 0, 0, 0, 127) : imagecolorallocate($image, 250, 250, 245));
    imagealphablending($image, true);
    imagesetthickness($image, 6);
    imageline($image, 80, 120, 320, 80, imagecolorallocate($image, 20, 20, 60));

    ob_start();
    $format === 'jpg' ? imagejpeg($image, null, 90) : imagepng($image);

    return ob_get_clean();
}

function asGuestCreator(ExpenseReport $report): array
{
    return [ExpenseReportPolicy::SESSION_GUEST_SLUGS => [$report->slug]];
}

// ---------- Membuat & membuka LPJ ----------

test('tamu bisa membuat LPJ dan membukanya di sesi yang sama', function () {
    $response = $this->post(route('lpj.store'), ['title' => 'Lomba Robotik', 'creator_name' => 'Deni']);

    $report = ExpenseReport::sole();
    $response->assertRedirect(route('lpj.show', $report->slug));

    expect($report->slug)->toStartWith('lomba-robotik-')
        ->and($report->share_token)->toHaveLength(32)
        ->and($report->user_id)->toBeNull()
        ->and(session(ExpenseReportPolicy::SESSION_GUEST_SLUGS))->toBe([$report->slug]);

    $this->get(route('lpj.show', $report->slug))->assertOk()->assertSee('Lomba Robotik');
});

test('user login menjadi pemilik LPJ yang dibuatnya', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('lpj.store'), ['title' => 'Study Tour', 'creator_name' => 'Deni']);

    expect(ExpenseReport::sole()->user_id)->toBe($user->id);
});

test('orang lain tidak bisa membuka LPJ tanpa izin', function () {
    $report = makeReport(['user_id' => User::factory()->create()->id]);

    $this->get(route('lpj.show', $report->slug))->assertForbidden();
    $this->actingAs(User::factory()->create())->get(route('lpj.show', $report->slug))->assertForbidden();
});

test('validasi judul dan penanggung jawab wajib diisi', function () {
    $this->post(route('lpj.store'), [])->assertSessionHasErrors(['title', 'creator_name']);
});

// ---------- Transaksi & nota ----------

test('menambah transaksi beserta foto nota', function () {
    $report = makeReport();

    $this->withSession(asGuestCreator($report))
        ->postJson(route('lpj.entry.store', $report->slug), [
            'type' => 'credit',
            'description' => 'Beli makan siang',
            'amount' => 150000,
            'images' => [UploadedFile::fake()->image('nota.jpg')],
        ])
        ->assertOk()
        ->assertJson(['success' => true, 'message' => 'Transaksi berhasil disimpan']);

    $entry = $report->entries()->with('images')->sole();
    expect($entry->images)->toHaveCount(1);
    Storage::disk('public')->assertExists($entry->images->first()->image_path);
});

test('mengubah transaksi: hapus nota lama, tambah nota baru', function () {
    $report = makeReport();
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'Lama', 'amount' => 1000]);
    $entry->attachImages([UploadedFile::fake()->image('lama.jpg')]);
    $oldImage = $entry->images()->sole();

    $this->withSession(asGuestCreator($report))
        ->patchJson(route('lpj.entry.update', $entry), [
            'type' => 'debit',
            'description' => 'Baru',
            'amount' => 5000,
            'deleted_images' => [$oldImage->id],
            'images' => [UploadedFile::fake()->image('baru.jpg')],
        ])
        ->assertOk()
        ->assertJson(['success' => true]);

    $entry->refresh();
    expect($entry->description)->toBe('Baru')
        ->and($entry->type)->toBe('debit')
        ->and($entry->images)->toHaveCount(1)
        ->and($entry->images->first()->id)->not->toBe($oldImage->id);

    Storage::disk('public')->assertMissing($oldImage->image_path);
});

test('id nota milik transaksi lain tidak ikut terhapus', function () {
    $report = makeReport();
    $entryA = $report->entries()->create(['type' => 'credit', 'description' => 'A', 'amount' => 1]);
    $entryB = $report->entries()->create(['type' => 'credit', 'description' => 'B', 'amount' => 1]);
    $entryB->attachImages([UploadedFile::fake()->image('b.jpg')]);
    $imageB = $entryB->images()->sole();

    $this->withSession(asGuestCreator($report))->patchJson(route('lpj.entry.update', $entryA), [
        'type' => 'credit', 'description' => 'A', 'amount' => 1,
        'deleted_images' => [$imageB->id],
    ])->assertOk();

    expect($imageB->fresh())->not->toBeNull();
});

test('menghapus transaksi ikut menghapus file nota', function () {
    $report = makeReport();
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'X', 'amount' => 1]);
    $entry->attachImages([UploadedFile::fake()->image('x.jpg')]);
    $path = $entry->images()->sole()->image_path;

    $this->withSession(asGuestCreator($report))
        ->deleteJson(route('lpj.entry.destroy', $entry))
        ->assertOk();

    expect(ExpenseEntry::count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});

test('pemilik menghapus laporan beserta semua file nota', function () {
    $user = User::factory()->create();
    $report = makeReport(['user_id' => $user->id]);
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'X', 'amount' => 1]);
    $entry->attachImages([UploadedFile::fake()->image('x.jpg')]);
    $path = $entry->images()->sole()->image_path;

    $this->actingAs(User::factory()->create())->deleteJson(route('lpj.destroy', $report))->assertForbidden();
    $this->actingAs($user)->deleteJson(route('lpj.destroy', $report))->assertOk();

    expect(ExpenseReport::count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});

// ---------- Judul / penanggung jawab ----------

test('mengubah judul dan penanggung jawab mengembalikan nilai baru', function () {
    $report = makeReport();
    $session = asGuestCreator($report);

    $this->withSession($session)->putJson(route('lpj.update-title', $report->slug), ['title' => 'Judul Baru'])
        ->assertOk()->assertJson(['new_title' => 'Judul Baru']);

    $this->withSession($session)->putJson(route('lpj.update-creator', $report->slug), ['creator_name' => 'Ust Aqil'])
        ->assertOk()->assertJson(['new_name' => 'Ust Aqil']);

    expect($report->fresh())->title->toBe('Judul Baru')->creator_name->toBe('Ust Aqil');
});

// ---------- Saldo & PDF ----------

test('saldo dihitung dari debit dikurangi kredit', function () {
    $user = User::factory()->create();
    $report = makeReport(['user_id' => $user->id]);
    $report->entries()->createMany([
        ['type' => 'debit', 'description' => 'Dana masuk', 'amount' => 1000000],
        ['type' => 'credit', 'description' => 'Belanja', 'amount' => 250000],
    ]);

    expect($report->balance)->toBe(750000.0)
        ->and($report->load('entries')->balance)->toBe(750000.0)
        ->and(ExpenseReport::withTotals()->find($report->id)->balance)->toBe(750000.0);

    $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Rp 750.000');
    $this->actingAs($user)->get(route('lpj.show', $report->slug))->assertSee('Rp 750.000');
});

test('download PDF menghasilkan file PDF', function () {
    $report = makeReport();
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'Nota', 'amount' => 5000]);
    $entry->attachImages([UploadedFile::fake()->image('nota.png')]);

    $report->storeSignature(SignatureImage::process(signatureImage()));
    $response = $this->withSession(asGuestCreator($report))->get(route('lpj.download', $report->slug));

    $response->assertOk()->assertHeader('content-type', 'application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('LPJ - Lomba Robotik.pdf');
});

// ---------- Bagikan link ----------

test('pemilik bisa mematikan link share (nilai "false" dari jQuery)', function () {
    $user = User::factory()->create();
    $report = makeReport(['user_id' => $user->id, 'is_shared' => true]);

    $this->actingAs($user)->post(route('lpj.share.update', $report), ['is_shared' => 'false', 'share_role' => 'viewer'])
        ->assertOk()->assertJson(['share_token' => $report->share_token]);

    expect($report->fresh()->is_shared)->toBeFalse();
});

test('hanya pemilik login yang bisa mengatur share', function () {
    $report = makeReport(); // milik tamu

    $this->withSession(asGuestCreator($report))
        ->postJson(route('lpj.share.update', $report), ['is_shared' => 'true'])
        ->assertUnauthorized();

    $this->actingAs(User::factory()->create())
        ->postJson(route('lpj.share.update', $report), ['is_shared' => 'true'])
        ->assertForbidden();
});

test('penerima link viewer hanya bisa melihat', function () {
    $report = makeReport(['user_id' => User::factory()->create()->id, 'is_shared' => true, 'share_role' => 'viewer']);

    $this->get(route('lpj.share', $report->share_token))->assertRedirect(route('lpj.show', $report->slug));
    $this->get(route('lpj.show', $report->slug))->assertOk();

    $this->postJson(route('lpj.entry.store', $report->slug), ['type' => 'debit', 'description' => 'x', 'amount' => 1])
        ->assertForbidden()
        ->assertJson(['message' => 'Anda hanya memiliki izin pelihat (viewer).']);
});

test('penerima link editor bisa menambah transaksi tapi tidak bisa menghapus laporan', function () {
    $report = makeReport(['user_id' => User::factory()->create()->id, 'is_shared' => true, 'share_role' => 'editor']);

    $this->get(route('lpj.share', $report->share_token));

    $this->postJson(route('lpj.entry.store', $report->slug), ['type' => 'debit', 'description' => 'x', 'amount' => 1])
        ->assertOk();

    $this->actingAs(User::factory()->create())->deleteJson(route('lpj.destroy', $report))->assertForbidden();
});

test('link share yang dimatikan tidak bisa dibuka', function () {
    $report = makeReport(['is_shared' => false]);

    $this->get(route('lpj.share', $report->share_token))->assertNotFound();
});

test('tabel & PDF memakai nomor urut sesuai urutan input, tanpa tanggal', function () {
    $report = makeReport();
    // Tanggal sengaja dibalik: urutan tetap mengikuti urutan input.
    $report->entries()->create(['type' => 'debit', 'description' => 'Dana awal', 'amount' => 1000, 'created_at' => '2026-10-21']);
    $belanja = $report->entries()->create(['type' => 'credit', 'description' => 'Belanja', 'amount' => 500, 'created_at' => '2026-10-20']);
    $belanja->attachImages([UploadedFile::fake()->image('nota.jpg')]);

    $html = $this->withSession(asGuestCreator($report))->get(route('lpj.show', $report->slug))->assertOk()->getContent();
    expect($html)->not->toContain('20/10/26')->not->toContain('edit_created_at')
        ->and(strpos($html, 'Dana awal'))->toBeLessThan(strpos($html, 'Belanja'))
        ->and($html)->toContain('2. Belanja'); // judul galeri nota

    $report->load('entries.images');
    $pdfHtml = view('lpj.pdf_template', compact('report'))->render();
    expect($pdfHtml)->toContain('>No</th>')->not->toContain('20/10/2026')
        ->and(preg_match_all('/<td class="no">(\d+)<\/td>/', $pdfHtml, $m))->toBe(2)
        ->and($m[1])->toBe(['1', '2'])
        ->and($pdfHtml)->toContain('<td class="nota-no">02</td><td class="nota-desc">Belanja</td>')
        ->and($pdfHtml)->toContain('<td>-</td>') // debit tanpa bukti
        ->and($pdfHtml)->toContain('<td>Terlampir</td>')
        ->and($pdfHtml)->not->toContain('Jumlah transaksi')
        ->and($pdfHtml)->not->toContain('Nomor lampiran sesuai');
});

test('tanggal tidak bisa diubah lewat edit transaksi', function () {
    $report = makeReport();
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'X', 'amount' => 1]);
    $original = $entry->created_at->toDateTimeString();

    $this->withSession(asGuestCreator($report))->patchJson(route('lpj.entry.update', $entry), [
        'type' => 'credit', 'description' => 'X', 'amount' => 1, 'created_at' => '2000-01-01',
    ])->assertOk();

    expect($entry->fresh()->created_at->toDateTimeString())->toBe($original);
});

test('PDF: saldo berjalan, minus dalam kurung, dan terbilang', function () {
    $report = makeReport();
    $report->entries()->create(['type' => 'debit', 'description' => 'Anggaran', 'amount' => 799000]);
    $report->entries()->create(['type' => 'credit', 'description' => 'Kelas', 'amount' => 803000]);
    $report->load('entries.images');

    $html = view('lpj.pdf_template', compact('report'))->render();

    expect($html)->toContain('<td class="num">799.000</td>')
        ->toContain('<td class="num">(4.000)</td>')       // saldo berjalan baris 2
        ->toContain('<td class="val">-Rp4.000</td>')        // saldo akhir pakai tanda minus
        ->not->toContain('(Rp4.000)')
        ->toContain('Minus Empat Ribu Rupiah')
        ->toContain('LAPORAN PERTANGGUNGJAWABAN')
        ->toContain('Hormat Kami,');
});

test('PDF: lampiran memberi tanda + untuk debit dan - untuk kredit', function () {
    $report = makeReport();
    $entry = $report->entries()->create(['type' => 'credit', 'description' => 'Bensin', 'amount' => 50000]);
    $entry->attachImages([UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')]);
    $report->load('entries.images');

    $html = view('lpj.pdf_template', compact('report'))->render();

    expect($html)->toContain('LAMPIRAN BUKTI TRANSAKSI')
        ->toContain('Bensin (1/2)')
        ->toContain('Bensin (2/2)')
        ->toContain('<div class="card-foot">-50.000</div>')
        ->not->toContain('Kredit ·');
});

test('Terbilang mengubah angka ke kata', function () {
    expect(\App\Support\Terbilang::make(7735000))->toBe('Tujuh Juta Tujuh Ratus Tiga Puluh Lima Ribu')
        ->and(\App\Support\Terbilang::make(1100))->toBe('Seribu Seratus')
        ->and(\App\Support\Terbilang::make(0))->toBe('Nol')
        ->and(\App\Support\Terbilang::rupiah(-4000))->toBe('Minus Empat Ribu Rupiah');
});

test('input nota menampilkan petunjuk tempel gambar (Ctrl+V)', function () {
    $report = makeReport();

    $html = $this->withSession(asGuestCreator($report))->get(route('lpj.show', $report->slug))->assertOk()->getContent();

    expect($html)->toContain('data-receipt-picker')
        ->toContain('Bisa juga tempel gambar nota');
});

test('judul tab dan footer memakai ejaan LPJ Maker, bukan APP_NAME', function () {
    config(['app.name' => 'LpjMaker | Make a finance report as fast as closing eyes']);
    $report = makeReport();

    $html = $this->withSession(asGuestCreator($report))->get(route('lpj.show', $report->slug))->assertOk()->getContent();

    expect($html)->toContain('<title>LPJ Maker — Make a finance report as fast as closing eyes</title>')
        ->toContain('LPJ Maker | Make a finance report as fast as closing eyes. <span')
        ->not->toContain('LpjMaker');
});
