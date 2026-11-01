<?php

namespace App\Http\Requests\Lpj;

use App\Models\ExpenseEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEntryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in([ExpenseEntry::TYPE_DEBIT, ExpenseEntry::TYPE_CREDIT])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    /** Kolom yang disimpan ke tabel expense_entries. */
    public function entryData(): array
    {
        return $this->safe()->only(['type', 'description', 'amount']);
    }

    /** @return array<\Illuminate\Http\UploadedFile> */
    public function uploadedImages(): array
    {
        return $this->file('images', []);
    }
}
