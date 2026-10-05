<?php

namespace App\Http\Requests;

use App\Models\Borrowing;
use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Borrowing::class) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $itemRules = [
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'string', 'exists:items,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'items.*.condition_before' => ['nullable', 'string', 'in:GOOD,MINOR_DAMAGE,DAMAGED'],
        ];

        return array_merge($itemRules, [
            'borrower_name' => ['required', 'string', 'max:150'],
            'borrower_identifier' => ['nullable', 'string', 'max:100'],
            'borrower_contact' => ['nullable', 'string', 'max:100'],
            'purpose' => ['nullable', 'string', 'max:500'],
            'borrowed_at' => ['nullable', 'date'],
            'expected_return_at' => ['nullable', 'date', 'after_or_equal:borrowed_at'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = parent::validated();
        $items = [];

        foreach ($data['items'] as $row) {
            $items[] = [
                'item_id' => $row['item_id'],
                'quantity' => (int) $row['quantity'],
                'condition_before' => $row['condition_before'] ?? 'GOOD',
            ];
        }

        $data['items'] = $items;

        return $data;
    }
}
