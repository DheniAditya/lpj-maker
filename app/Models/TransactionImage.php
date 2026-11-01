<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TransactionImage extends Model
{
    public const DISK = 'public';

    public const DIRECTORY = 'receipts';

    protected $fillable = ['expense_entry_id', 'image_path'];

    protected static function booted(): void
    {
        static::deleted(function (TransactionImage $image) {
            Storage::disk(self::DISK)->delete($image->image_path);
        });
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ExpenseEntry::class, 'expense_entry_id');
    }

    /** URL publik gambar (lewat symlink public/storage). */
    public function url(): string
    {
        return asset('storage/'.$this->image_path);
    }

    /**
     * Gambar sebagai data URI untuk DomPDF, atau null jika file fisiknya hilang.
     */
    public function toDataUri(): ?string
    {
        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($this->image_path)) {
            return null;
        }

        $mime = $disk->mimeType($this->image_path) ?: 'image/jpeg';

        return "data:{$mime};base64,".base64_encode($disk->get($this->image_path));
    }
}
