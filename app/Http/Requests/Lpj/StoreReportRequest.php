<?php

namespace App\Http\Requests\Lpj;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'creator_name' => ['required', 'string', 'max:255'],
        ];
    }
}
