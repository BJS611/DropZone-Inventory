<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('category')) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:categories,code,'.$this->route('category')?->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function validatedData(): array
    {
        $data = parent::validated();
        $data['code'] = mb_strtoupper(trim((string) $data['code']));

        return $data;
    }
}
