<?php

namespace App\Support;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Build the filtered/sorted inventory query from request parameters.
 */
class ItemQuery
{
    public const SORTABLE = [
        'name',
        'sku',
        'quantity',
        'minimum_stock',
        'created_at',
        'updated_at',
    ];

    public const PER_PAGE = [10, 20, 50, 100];

    /**
     * @return Builder<Item>
     */
    public function build(Request $request): Builder
    {
        $query = Item::query()
            ->with(['category', 'location', 'supplier']);

        if ($search = $this->search($request)) {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('sku', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        $query->when($request->filled('category'), function (Builder $q) use ($request): void {
            $q->where('category_id', $request->string('category')->toString());
        });

        $query->when($request->filled('location'), function (Builder $q) use ($request): void {
            $q->where('location_id', $request->string('location')->toString());
        });

        $query->when($request->filled('supplier'), function (Builder $q) use ($request): void {
            $q->where('supplier_id', $request->string('supplier')->toString());
        });

        $query->when($request->filled('status'), function (Builder $q) use ($request): void {
            $q->where('status', $request->string('status')->toString());
        });

        $query->when($request->filled('condition'), function (Builder $q) use ($request): void {
            $q->where('condition', $request->string('condition')->toString());
        });

        $query->when($request->filled('stock_status'), function (Builder $q) use ($request): void {
            $stockStatus = $request->string('stock_status')->toString();

            $q->when($stockStatus === 'NORMAL', function (Builder $inner): void {
                $inner->whereColumn('quantity', '>', 'minimum_stock');
            })
                ->when($stockStatus === 'LOW', function (Builder $inner): void {
                    $inner->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'minimum_stock');
                })
                ->when($stockStatus === 'OUT_OF_STOCK', function (Builder $inner): void {
                    $inner->where('quantity', 0);
                });
        });

        return $query->orderBy(
            $this->sortColumn($request),
            $this->sortDirection($request),
        );
    }

    public function perPage(Request $request): int
    {
        $value = (int) $request->input('per_page', 20);

        return in_array($value, self::PER_PAGE, true) ? $value : 20;
    }

    protected function search(Request $request): ?string
    {
        $search = $request->string('search')->trim()->toString();

        return $search === '' ? null : $search;
    }

    protected function sortColumn(Request $request): string
    {
        $column = $request->string('sort')->toString();

        return in_array($column, self::SORTABLE, true) ? $column : 'updated_at';
    }

    protected function sortDirection(Request $request): string
    {
        $direction = $request->string('direction')->upper()->toString();

        return $direction === 'ASC' ? 'asc' : 'desc';
    }

    /**
     * Validate filter values coming from the query string.
     *
     * @return array<string, string>
     */
    public function validationRules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'uuid'],
            'location' => ['nullable', 'string', 'uuid'],
            'supplier' => ['nullable', 'string', 'uuid'],
            'status' => ['nullable', 'string', 'in:'.implode(',', array_column(ItemStatus::cases(), 'value'))],
            'condition' => ['nullable', 'string', 'in:'.implode(',', array_column(ItemCondition::cases(), 'value'))],
            'stock_status' => ['nullable', 'string', 'in:NORMAL,LOW,OUT_OF_STOCK'],
            'sort' => ['nullable', 'string', 'in:'.implode(',', self::SORTABLE)],
            'direction' => ['nullable', 'string', 'in:asc,desc,ASC,DESC'],
            'per_page' => ['nullable', 'integer', 'in:'.implode(',', self::PER_PAGE)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
