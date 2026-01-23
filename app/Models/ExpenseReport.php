<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseReport extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Satu LPJ (Report) punya BANYAK Entri
    public function entries(): HasMany
    {
        return $this->hasMany(ExpenseEntry::class)->orderBy('created_at', 'asc');
    }
    
    // --- Accessor (Getter) untuk menghitung total ---

    // Menghitung Total Uang Masuk (Debit)
    public function getTotalDebitAttribute(): float
    {
        return $this->entries()->where('type', 'debit')->sum('amount');
    }

    // Menghitung Total Uang Keluar (Kredit)
    public function getTotalCreditAttribute(): float
    {
        return $this->entries()->where('type', 'credit')->sum('amount');
    }

    // Menghitung Sisa Saldo
    public function getBalanceAttribute(): float
        {
            return $this->total_debit - $this->total_credit;
        }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
            return $this->belongsTo(User::class);
        }
}