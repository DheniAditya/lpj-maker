<?php

use App\Models\ExpenseReport;
use App\Models\User;
use App\Policies\ExpenseReportPolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->report = ExpenseReport::create(['title' => 'Lomba', 'creator_name' => 'Deni']);
    $this->session = [ExpenseReportPolicy::SESSION_GUEST_SLUGS => [$this->report->slug]];
});

function storedSignature(ExpenseReport $report): GdImage
{
    $report->refresh();
    Storage::disk('public')->assertExists($report->signature_path);

    return imagecreatefromstring(Storage::disk('public')->get($report->signature_path));
}

function alphaAt(GdImage $image, int $x, int $y): int
{
    return (imagecolorat($image, $x, $y) >> 24) & 0x7F;
}

test('PDF tidak bisa diunduh sebelum ada tanda tangan', function () {
    $this->withSession($this->session)
        ->get(route('lpj.download', $this->report->slug))
        ->assertRedirect(route('lpj.show', $this->report->slug))
        ->assertSessionHas('error');

    $this->withSession($this->session)->get(route('lpj.show', $this->report->slug))
        ->assertSee('Tambahkan tanda tangan untuk bisa mengunduh PDF.');
});

test('upload JPG berlatar putih: latar jadi transparan dan area kosong dipotong', function () {
    $file = UploadedFile::fake()->createWithContent('ttd.jpg', signatureImage(format: 'jpg'));

    $this->withSession($this->session)
        ->postJson(route('lpj.signature.store', $this->report->slug), ['signature' => $file])
        ->assertOk()->assertJson(['success' => true]);

    $image = storedSignature($this->report);

    expect(alphaAt($image, 0, 0))->toBe(127)               // pojok (latar) transparan
        ->and(imagesx($image))->toBeLessThan(400)           // dipotong
        ->and(imagesy($image))->toBeLessThan(200);

    // ada piksel tinta yang tidak transparan
    $opaque = 0;
    for ($x = 0; $x < imagesx($image); $x += 2) {
        for ($y = 0; $y < imagesy($image); $y += 2) {
            $opaque += alphaAt($image, $x, $y) < 20 ? 1 : 0;
        }
    }
    expect($opaque)->toBeGreaterThan(50);
});

test('tanda tangan dari canvas (data URL PNG transparan) disimpan', function () {
    $dataUrl = 'data:image/png;base64,'.base64_encode(signatureImage(transparent: true));

    $this->withSession($this->session)
        ->postJson(route('lpj.signature.store', $this->report->slug), ['signature_data' => $dataUrl])
        ->assertOk();

    expect(alphaAt(storedSignature($this->report), 0, 0))->toBe(127);

    // setelah ada tanda tangan, PDF bisa diunduh
    $this->withSession($this->session)->get(route('lpj.download', $this->report->slug))
        ->assertOk()->assertHeader('content-type', 'application/pdf');
});

test('canvas kosong ditolak dengan pesan yang jelas', function () {
    $empty = imagecreatetruecolor(300, 100);
    imagesavealpha($empty, true);
    imagealphablending($empty, false);
    imagefill($empty, 0, 0, imagecolorallocatealpha($empty, 0, 0, 0, 127));
    ob_start();
    imagepng($empty);
    $dataUrl = 'data:image/png;base64,'.base64_encode(ob_get_clean());

    $this->withSession($this->session)
        ->postJson(route('lpj.signature.store', $this->report->slug), ['signature_data' => $dataUrl])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('signature');
});

test('mengganti tanda tangan menghapus file lama, dan bisa dihapus', function () {
    $this->withSession($this->session)->postJson(route('lpj.signature.store', $this->report->slug), [
        'signature_data' => 'data:image/png;base64,'.base64_encode(signatureImage(transparent: true)),
    ]);
    $old = $this->report->fresh()->signature_path;

    $this->withSession($this->session)->postJson(route('lpj.signature.store', $this->report->slug), [
        'signature' => UploadedFile::fake()->createWithContent('ttd.png', signatureImage()),
    ])->assertOk();

    Storage::disk('public')->assertMissing($old);

    $this->withSession($this->session)->deleteJson(route('lpj.signature.destroy', $this->report->slug))->assertOk();
    expect($this->report->fresh()->signature_path)->toBeNull();
});

test('penerima link pelihat tidak bisa menandatangani', function () {
    $report = ExpenseReport::create(['title' => 'X', 'creator_name' => 'Y', 'user_id' => User::factory()->create()->id, 'is_shared' => true]);
    $this->get(route('lpj.share', $report->share_token));

    $this->postJson(route('lpj.signature.store', $report->slug), [
        'signature_data' => 'data:image/png;base64,'.base64_encode(signatureImage(transparent: true)),
    ])->assertForbidden();

    $this->get(route('lpj.show', $report->slug))->assertSee('Menunggu tanda tangan dari pembuat LPJ.');
});

test('menghapus LPJ ikut menghapus file tanda tangan', function () {
    $this->report->storeSignature(\App\Support\SignatureImage::process(signatureImage()));
    $path = $this->report->signature_path;

    $this->report->delete();

    Storage::disk('public')->assertMissing($path);
});
