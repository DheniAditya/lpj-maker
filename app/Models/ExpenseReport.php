<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseReport extends Model
{
    use HasFactory;

    public const ROLE_VIEWER = 'viewer';

    public const ROLE_EDITOR = 'editor';

    public const SIGNATURE_DIRECTORY = 'signatures';

    protected $guarded = ['id'];

    protected $casts = [
        'is_shared' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ExpenseReport $report) {
            $report->slug ??= Str::slug($report->title).'-'.uniqid();
            $report->share_token ??= Str::random(32);
        });

        // Hapus entri satu per satu agar file nota ikut terhapus (lihat ExpenseEntry::booted).
        static::deleting(function (ExpenseReport $report) {
            $report->entries->each->delete();
            $report->deleteSignatureFile();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Urutan = urutan input; dipakai sebagai nomor transaksi (1, 2, 3, ...). */
    public function entries(): HasMany
    {
        return $this->hasMany(ExpenseEntry::class)->orderBy('id');
    }

    // ---------- Syarat download PDF ----------

    /**
     * Nomor urut transaksi Kredit (uang keluar) yang belum punya nota.
     * Debit (uang masuk) boleh tanpa nota.
     *
     * @return array<int, int>
     */
    public function creditNumbersMissingReceipt(): array
    {
        $entries = $this->relationLoaded('entries') ? $this->entries : $this->entries()->with('images')->get();

        return $entries->values()
            ->map(fn (ExpenseEntry $entry, int $i) => [
                'number' => $i + 1,
                'missing' => ! $entry->isDebit() && $entry->images->isEmpty(),
            ])
            ->where('missing', true)
            ->pluck('number')
            ->all();
    }

    public function canDownloadPdf(): bool
    {
        return $this->hasSignature() && $this->creditNumbersMissingReceipt() === [];
    }

    // ---------- Tanda tangan ----------

    public function hasSignature(): bool
    {
        return $this->signature_path !== null
            && Storage::disk(TransactionImage::DISK)->exists($this->signature_path);
    }

    public function signatureUrl(): ?string
    {
        return $this->signature_path ? asset('storage/'.$this->signature_path).'?v='.$this->updated_at?->timestamp : null;
    }

    /** Untuk DomPDF (gambar disematkan langsung ke PDF). */
    public function signatureDataUri(): ?string
    {
        if (! $this->hasSignature()) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode(Storage::disk(TransactionImage::DISK)->get($this->signature_path));
    }

    /** Simpan PNG tanda tangan baru, ganti yang lama. */
    public function storeSignature(string $png): void
    {
        $path = self::SIGNATURE_DIRECTORY.'/'.$this->slug.'-'.Str::random(8).'.png';
        Storage::disk(TransactionImage::DISK)->put($path, $png);

        $this->deleteSignatureFile();
        $this->update(['signature_path' => $path]);
    }

    public function removeSignature(): void
    {
        $this->deleteSignatureFile();
        $this->update(['signature_path' => null]);
    }

    private function deleteSignatureFile(): void
    {
        if ($this->signature_path) {
            Storage::disk(TransactionImage::DISK)->delete($this->signature_path);
        }
    }

    /**
     * Ambil total debit & kredit dalam query yang sama (untuk daftar laporan),
     * supaya tidak ada 2 query tambahan per laporan.
     */
    public function scopeWithTotals(Builder $query): Builder
    {
        return $query
            ->withSum(['entries as debit_sum' => fn ($q) => $q->where('type', ExpenseEntry::TYPE_DEBIT)], 'amount')
            ->withSum(['entries as credit_sum' => fn ($q) => $q->where('type', ExpenseEntry::TYPE_CREDIT)], 'amount');
    }

    protected function totalDebit(): Attribute
    {
        return Attribute::get(fn () => $this->sumByType(ExpenseEntry::TYPE_DEBIT));
    }

    protected function totalCredit(): Attribute
    {
        return Attribute::get(fn () => $this->sumByType(ExpenseEntry::TYPE_CREDIT));
    }

    protected function balance(): Attribute
    {
        return Attribute::get(fn () => $this->total_debit - $this->total_credit);
    }

    /**
     * Urutan sumber data: hasil withTotals() → relasi entries yang sudah dimuat → query.
     */
    private function sumByType(string $type): float
    {
        $preloaded = "{$type}_sum";

        if (array_key_exists($preloaded, $this->attributes)) {
            return (float) $this->attributes[$preloaded];
        }

        if ($this->relationLoaded('entries')) {
            return (float) $this->entries->where('type', $type)->sum('amount');
        }

        return (float) $this->entries()->where('type', $type)->sum('amount');
    }
}
