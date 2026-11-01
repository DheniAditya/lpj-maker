<?php

namespace App\Http\Requests\Lpj;

use App\Models\ExpenseReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShareRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_shared' => ['required', 'in:true,false,1,0'],
            'share_role' => ['nullable', Rule::in([ExpenseReport::ROLE_VIEWER, ExpenseReport::ROLE_EDITOR])],
        ];
    }
}
