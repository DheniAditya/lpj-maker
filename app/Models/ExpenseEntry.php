<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ExpenseEntry extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Satu Entri milik SATU Report
    public function images()
{
    return $this->hasMany(TransactionImage::class, 'expense_entry_id');
}

// Relasi balik ke laporan (jika belum ada)
public function expenseReport()
{
    return $this->belongsTo(ExpenseReport::class);
}

    }
