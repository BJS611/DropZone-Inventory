<?php

namespace App\Http\Requests;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\Unit;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Item::class) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:100', 'unique:items,sku'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'location_id' => ['required', 'string', 'exists:locations,id'],
            'supplier_id' => ['nullable', 'string', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:999999'],
            'minimum_stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'unit' => ['required', 'string', 'in:'.implode(',', array_column(Unit::cases(), 'value'))],
            'condition' => ['required', 'string', 'in:'.implode(',', array_column(ItemCondition::cases(), 'value'))],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(ItemStatus::cases(), 'value'))],
            'image_url' => ['nullable', 'string', 'url', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = parent::validated();
        $data['quantity'] = (int) $data['quantity'];
        $data['minimum_stock'] = (int) $data['minimum_stock'];
        $data['sku'] = mb_strtoupper(trim((string) $data['sku']));

        return $data;
    }
}
