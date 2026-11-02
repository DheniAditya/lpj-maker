<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => User::usernameRules($this->user()->id),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['username' => User::normalizeUsername($this->input('username'))]);
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, titik, garis bawah, atau strip (tanpa spasi).',
            'username.unique' => 'Username ini sudah dipakai. Coba yang lain.',
        ];
    }
}
