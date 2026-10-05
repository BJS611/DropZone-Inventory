<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('location')) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:locations,code,'.$this->route('location')?->id],
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
