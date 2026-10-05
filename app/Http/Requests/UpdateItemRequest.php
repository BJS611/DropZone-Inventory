<?php

namespace App\Http\Requests;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\Unit;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('item')) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'location_id' => ['required', 'string', 'exists:locations,id'],
            'supplier_id' => ['nullable', 'string', 'exists:suppliers,id'],
            'minimum_stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'unit' => ['required', 'string', 'in:'.implode(',', array_column(Unit::cases(), 'value'))],
            'condition' => ['required', 'string', 'in:'.implode(',', array_column(ItemCondition::cases(), 'value'))],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(ItemStatus::cases(), 'value'))],
            'image_url' => ['nullable', 'string', 'url', 'max:500'],
        ];
    }

    /**
     * Kuantitas sengaja tidak disertakan: stok hanya boleh diubah lewat
     * transaksi stok (IN / OUT / ADJUSTMENT / TRANSFER).
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = parent::validated();
        $data['minimum_stock'] = (int) $data['minimum_stock'];

        return $data;
    }
}
