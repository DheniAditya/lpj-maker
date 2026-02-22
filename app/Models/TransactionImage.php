<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionImage extends Model
{
    protected $fillable = ['expense_entry_id', 'image_path'];

    public function entry()
    {
        return $this->belongsTo(ExpenseEntry::class, 'expense_entry_id');
    }
}