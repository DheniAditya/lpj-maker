<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;

class ExpenseEntry extends Model
{
    use HasFactory;

    public const TYPE_DEBIT = 'debit';

    public const TYPE_CREDIT = 'credit';

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        // Hapus gambar satu per satu agar file fisiknya ikut terhapus (lihat TransactionImage::booted).
        static::deleting(function (ExpenseEntry $entry) {
            $entry->images->each->delete();
        });
    }

    public function expenseReport(): BelongsTo
    {
        return $this->belongsTo(ExpenseReport::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TransactionImage::class);
    }

    public function isDebit(): bool
    {
        return $this->type === self::TYPE_DEBIT;
    }

    /**
     * Simpan file nota ke disk public (folder receipts) dan catat di database.
     *
     * @param  array<UploadedFile>  $files
     */
    public function attachImages(array $files): void
    {
        foreach ($files as $file) {
            $this->images()->create([
                'image_path' => $file->store(TransactionImage::DIRECTORY, TransactionImage::DISK),
            ]);
        }
    }

    /**
     * Hapus nota milik entri ini berdasarkan id (id milik entri lain diabaikan).
     *
     * @param  array<int|string>  $imageIds
     */
    public function detachImages(array $imageIds): void
    {
        if (! $imageIds) {
            return;
        }

        $this->images()->whereKey($imageIds)->get()->each->delete();
    }
}
