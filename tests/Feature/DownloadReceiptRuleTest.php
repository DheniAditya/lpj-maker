<?php

use App\Models\ExpenseReport;
use App\Policies\ExpenseReportPolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->report = ExpenseReport::create(['title' => 'Lomba', 'creator_name' => 'Deni']);
    $this->report->storeSignature(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));
    $this->session = [ExpenseReportPolicy::SESSION_GUEST_SLUGS => [$this->report->slug]];
});

function addEntry(ExpenseReport $report, string $type, bool $withReceipt)
{
    $entry = $report->entries()->create(['type' => $type, 'description' => 'Transaksi', 'amount' => 10000]);
    if ($withReceipt) {
        $entry->attachImages([UploadedFile::fake()->image('nota.jpg')]);
    }

    return $entry;
}

test('PDF tidak bisa diunduh selama ada transaksi Kredit tanpa nota', function () {
    addEntry($this->report, 'debit', false);   // no. 1 — Debit tanpa nota: boleh
    addEntry($this->report, 'credit', true);   // no. 2 — ada nota
    addEntry($this->report, 'credit', false);  // no. 3 — Kredit tanpa nota

    expect($this->report->fresh()->creditNumbersMissingReceipt())->toBe([3]);

    $this->withSession($this->session)
        ->get(route('lpj.download', $this->report->slug))
        ->assertRedirect(route('lpj.show', $this->report->slug))
        ->assertSessionHas('error', 'Nota belum lengkap. Download PDF terkunci.');

    $this->withSession($this->session)->get(route('lpj.show', $this->report->slug))
        ->assertOk()
        ->assertSee('Nota belum lengkap. Download PDF terkunci.')
        ->assertDontSee(route('lpj.download', $this->report->slug));
});

test('Debit tanpa nota tidak menghalangi download PDF', function () {
    addEntry($this->report, 'debit', false);
    addEntry($this->report, 'credit', true);

    expect($this->report->fresh()->canDownloadPdf())->toBeTrue();

    $this->withSession($this->session)->get(route('lpj.show', $this->report->slug))
        ->assertSee(route('lpj.download', $this->report->slug))
        ->assertDontSee('Nota belum lengkap');

    $this->withSession($this->session)->get(route('lpj.download', $this->report->slug))
        ->assertOk()->assertHeader('content-type', 'application/pdf');
});
