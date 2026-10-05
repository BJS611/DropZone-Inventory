<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('return', $this->route('borrowing')) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'condition_after' => ['nullable', 'string', 'in:GOOD,MINOR_DAMAGE,DAMAGED'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = parent::validated();
        $data['quantity'] = (int) $data['quantity'];

        return $data;
    }
}
