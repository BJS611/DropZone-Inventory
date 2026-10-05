<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function __construct(private AuditService $audit) {}

    /**
     * Tambah stok barang.
     *
     * @param  array{item_id:string,quantity:int,note:?string,location_id:?string}  $data
     */
    public function stockIn(Item $item, array $data, ?User $user = null): StockTransaction
    {
        $user ??= auth()->user();

        return DB::transaction(function () use ($item, $data, $user): StockTransaction {
            $item->increment('quantity', $data['quantity']);

            $transaction = $this->record($item, TransactionType::IN, [
                'quantity' => $data['quantity'],
                'to_location_id' => $data['location_id'] ?? $item->location_id,
                'note' => $data['note'] ?? null,
                'performed_by' => $user?->id,
            ]);

            $this->audit->log(
                'stock_in',
                'Item',
                $item->id,
                ['quantity' => $data['quantity'], 'note' => $data['note'] ?? null],
                $user,
            );

            return $transaction;
        });
    }

    /**
     * Kurangi stok barang. Stok tidak boleh negatif.
     *
     * @param  array{item_id:string,quantity:int,note:?string}  $data
     *
     * @throws ValidationException
     */
    public function stockOut(Item $item, array $data, ?User $user = null): StockTransaction
    {
        $user ??= auth()->user();

        return DB::transaction(function () use ($item, $data, $user): StockTransaction {
            if ($item->quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Stok saat ini: '.$item->quantity,
                ]);
            }

            $item->decrement('quantity', $data['quantity']);

            $transaction = $this->record($item, TransactionType::OUT, [
                'quantity' => $data['quantity'],
                'from_location_id' => $item->location_id,
                'note' => $data['note'] ?? null,
                'performed_by' => $user?->id,
            ]);

            $this->audit->log(
                'stock_out',
                'Item',
                $item->id,
                ['quantity' => $data['quantity'], 'note' => $data['note'] ?? null],
                $user,
            );

            return $transaction;
        });
    }

    /**
     * Sesuaikan stok ke nilai baru.
     *
     * @param  array{item_id:string,new_stock:int,note:?string}  $data
     */
    public function adjust(Item $item, array $data, ?User $user = null): StockTransaction
    {
        $user ??= auth()->user();

        return DB::transaction(function () use ($item, $data, $user): StockTransaction {
            $oldStock = (int) $item->quantity;
            $newStock = (int) $data['new_stock'];
            $difference = $newStock - $oldStock;

            if ($difference === 0) {
                throw ValidationException::withMessages([
                    'new_stock' => 'Stok baru sama dengan stok saat ini.',
                ]);
            }

            $item->update(['quantity' => $newStock]);

            $transaction = $this->record($item, TransactionType::ADJUSTMENT, [
                'quantity' => abs($difference),
                'note' => ($data['note'] ?? null) ?? 'Penyesuaian stok',
                'performed_by' => $user?->id,
            ]);

            $this->audit->log(
                'stock_adjustment',
                'Item',
                $item->id,
                [
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'note' => $data['note'] ?? null,
                ],
                $user,
            );

            return $transaction;
        });
    }

    /**
     * Pindahkan barang antar lokasi.
     *
     * @param  array{item_id:string,quantity:int,from_location_id:string,to_location_id:string,note:?string}  $data
     */
    public function transfer(Item $item, array $data, ?User $user = null): StockTransaction
    {
        $user ??= auth()->user();

        $validator = Validator::make($data, [
            'quantity' => 'required|integer|min:1',
            'from_location_id' => ['required', 'exists:locations,id'],
            'to_location_id' => ['required', 'exists:locations,id', 'different:from_location_id'],
        ]);

        $validator->validate();

        return DB::transaction(function () use ($item, $data, $user): StockTransaction {
            if ((string) $item->location_id !== (string) $data['from_location_id']) {
                throw ValidationException::withMessages([
                    'from_location_id' => 'Lokasi asal tidak sesuai dengan lokasi barang.',
                ]);
            }

            if ($item->quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi untuk transfer.',
                ]);
            }

            $destination = Location::lockForUpdate()->findOrFail($data['to_location_id']);

            $item->update(['location_id' => $destination->id]);

            $transaction = $this->record($item, TransactionType::TRANSFER, [
                'quantity' => $data['quantity'],
                'from_location_id' => $data['from_location_id'],
                'to_location_id' => $data['to_location_id'],
                'note' => $data['note'] ?? null,
                'performed_by' => $user?->id,
            ]);

            $this->audit->log(
                'stock_transfer',
                'Item',
                $item->id,
                [
                    'quantity' => $data['quantity'],
                    'from_location_id' => $data['from_location_id'],
                    'to_location_id' => $data['to_location_id'],
                    'note' => $data['note'] ?? null,
                ],
                $user,
            );

            return $transaction;
        });
    }

    /**
     * Tulis baris transaksi stok (append-only, tidak boleh diubah).
     *
     * @param  array{quantity:int,from_location_id:?string,to_location_id:?string,note:?string,performed_by:?string}  $data
     */
    public function record(Item $item, TransactionType $type, array $data): StockTransaction
    {
        return StockTransaction::create([
            'item_id' => $item->id,
            'type' => $type,
            'quantity' => $data['quantity'],
            'from_location_id' => $data['from_location_id'] ?? null,
            'to_location_id' => $data['to_location_id'] ?? null,
            'note' => $data['note'] ?? null,
            'performed_by' => $data['performed_by'] ?? null,
            'created_at' => now(),
        ]);
    }
}
