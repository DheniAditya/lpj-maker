<?php

namespace App\Http\Requests\Lpj;

class UpdateEntryRequest extends StoreEntryRequest
{
    public function rules(): array
    {
        return parent::rules() + [
            'deleted_images' => ['nullable', 'array'],
            'deleted_images.*' => ['integer'],
        ];
    }

    /** @return array<int> */
    public function deletedImageIds(): array
    {
        return $this->validated('deleted_images') ?? [];
    }
}
