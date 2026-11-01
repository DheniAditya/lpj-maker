<?php

namespace App\Http\Requests\Lpj;

class UpdateEntryRequest extends StoreEntryRequest
{
    public function rules(): array
    {
        return parent::rules() + [
            'created_at' => ['required', 'date'],
            'deleted_images' => ['nullable', 'array'],
            'deleted_images.*' => ['integer'],
        ];
    }

    public function entryData(): array
    {
        return $this->safe()->only(['type', 'description', 'amount', 'created_at']);
    }

    /** @return array<int> */
    public function deletedImageIds(): array
    {
        return $this->validated('deleted_images') ?? [];
    }
}
