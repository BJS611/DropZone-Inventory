<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('transfer', $this->route('item')) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'from_location_id' => ['required', 'string', 'exists:locations,id'],
            'to_location_id' => ['required', 'string', 'exists:locations,id', 'different:from_location_id'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
