<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ExpenseReport extends Model
{
    use HasFactory;

    public const ROLE_VIEWER = 'viewer';

    public const ROLE_EDITOR = 'editor';

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
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ExpenseEntry::class)->orderBy('created_at');
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
