<?php

namespace App\Services;

use App\Enums\BorrowingStatus;
use App\Enums\ItemCondition;
use App\Enums\TransactionType;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingService
{
    public function __construct(
        private AuditService $audit,
        private StockService $stocks,
    ) {}

    /**
     * Buat peminjaman baru dan kurangi stok secara atomik.
     *
     * @param  array{borrower_name:string,borrower_identifier:?string,borrower_contact:?string,purpose:?string,borrowed_at:?string,expected_return_at:?string,note:?string,items:array<int,array{item_id:string,quantity:int,condition_before:string}>}  $data
     */
    public function create(array $data, ?User $user = null): Borrowing
    {
        $user ??= auth()->user();

        return DB::transaction(function () use ($data, $user): Borrowing {
            $borrowing = Borrowing::create([
                'borrower_name' => $data['borrower_name'],
                'borrower_identifier' => $data['borrower_identifier'] ?? null,
                'borrower_contact' => $data['borrower_contact'] ?? null,
                'purpose' => $data['purpose'] ?? null,
                'borrowed_at' => $data['borrowed_at'] ?? now(),
                'expected_return_at' => $data['expected_return_at'] ?? null,
                'returned_at' => null,
                'status' => BorrowingStatus::BORROWED,
                'approved_by' => $user?->id,
                'created_by' => $user?->id,
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['items'] as $row) {
                /** @var Item $item */
                $item = Item::lockForUpdate()->findOrFail($row['item_id']);
                $quantity = (int) $row['quantity'];

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'items.'.$row['item_id'].'.quantity' => 'Jumlah pinjaman minimal 1.',
                    ]);
                }

                if ($item->availableQuantity() < $quantity) {
                    throw ValidationException::withMessages([
                        'items.'.$item->id.'.quantity' => sprintf(
                            'Stok tersedia untuk %s tidak mencukupi (tersedia %d).',
                            $item->name,
                            $item->availableQuantity(),
                        ),
                    ]);
                }

                $item->decrement('quantity', $quantity);

                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'condition_before' => $row['condition_before'] ?? ItemCondition::GOOD->value,
                    'condition_after' => null,
                ]);

                $this->stocks->record($item, TransactionType::OUT, [
                    'quantity' => $quantity,
                    'from_location_id' => $item->location_id,
                    'note' => 'Peminjaman '.$borrowing->borrower_name,
                    'performed_by' => $user?->id,
                ]);
            }

            $this->audit->log(
                'borrowing_create',
                'Borrowing',
                $borrowing->id,
                ['borrower_name' => $borrowing->borrower_name, 'items' => count($data['items'])],
                $user,
            );

            return $borrowing;
        });
    }

    /**
     * Kembalikan sebagian atau seluruh barang yang dipinjam.
     *
     * @param  array{quantity:int,condition_after:?string,note:?string}  $data
     */
    public function returnItem(BorrowingItem $borrowingItem, array $data, ?User $user = null): Borrowing
    {
        $user ??= auth()->user();

        return DB::transaction(function () use ($borrowingItem, $data, $user): Borrowing {
            $borrowing = $borrowingItem->borrowing()->lockForUpdate()->firstOrFail();
            $item = Item::lockForUpdate()->findOrFail($borrowingItem->item_id);

            $remaining = $borrowingItem->remainingQuantity();
            $quantity = (int) $data['quantity'];

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah pengembalian minimal 1.',
                ]);
            }

            if ($quantity > $remaining) {
                throw ValidationException::withMessages([
                    'quantity' => sprintf('Jumlah pengembalian melebihi sisa pinjaman (%d).', $remaining),
                ]);
            }

            $item->increment('quantity', $quantity);
            $borrowingItem->increment('returned_quantity', $quantity);

            if (! empty($data['condition_after'])) {
                $borrowingItem->update(['condition_after' => $data['condition_after']]);
            }

            $this->stocks->record($item, TransactionType::RETURN, [
                'quantity' => $quantity,
                'to_location_id' => $item->location_id,
                'note' => 'Pengembalian pinjaman '.$borrowing->borrower_name,
                'performed_by' => $user?->id,
            ]);

            $allReturned = $borrowing->fresh()->isFullyReturned();
            $partial = $borrowing->fresh()->totalReturnedQuantity() > 0;

            $borrowing->update([
                'status' => $allReturned ? BorrowingStatus::RETURNED : (
                    $partial ? BorrowingStatus::PARTIALLY_RETURNED : $borrowing->status
                ),
                'returned_at' => $allReturned ? now() : null,
            ]);

            $this->audit->log(
                'borrow_return',
                'Borrowing',
                $borrowing->id,
                [
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'full' => $allReturned,
                ],
                $user,
            );

            return $borrowing->fresh();
        });
    }

    /**
     * Batalkan peminjaman (hanya jika belum ada pengembalian).
     */
    public function cancel(Borrowing $borrowing, ?User $user = null): Borrowing
    {
        $user ??= auth()->user();

        return DB::transaction(function () use ($borrowing, $user): Borrowing {
            if ($borrowing->totalReturnedQuantity() > 0) {
                throw ValidationException::withMessages([
                    'borrowing' => 'Peminjaman yang sudah dikembalikan sebagian tidak dapat dibatalkan.',
                ]);
            }

            foreach ($borrowing->items as $borrowingItem) {
                $item = Item::lockForUpdate()->findOrFail($borrowingItem->item_id);
                $item->increment('quantity', $borrowingItem->quantity);

                $this->stocks->record($item, TransactionType::IN, [
                    'quantity' => $borrowingItem->quantity,
                    'to_location_id' => $item->location_id,
                    'note' => 'Pembatalan peminjaman '.$borrowing->borrower_name,
                    'performed_by' => $user?->id,
                ]);
            }

            $borrowing->update(['status' => BorrowingStatus::CANCELLED]);

            $this->audit->log(
                'borrowing_cancel',
                'Borrowing',
                $borrowing->id,
                null,
                $user,
            );

            return $borrowing->fresh();
        });
    }

    /**
     * Perbarui status peminjaman yang sudah lewat tanggal pengembalian.
     */
    public function refreshOverdue(): int
    {
        $borrowingIds = Borrowing::query()
            ->whereNotIn('status', [BorrowingStatus::RETURNED, BorrowingStatus::CANCELLED])
            ->whereNotNull('expected_return_at')
            ->where('expected_return_at', '<', now())
            ->pluck('id');

        foreach ($borrowingIds as $id) {
            Borrowing::whereKey($id)->update(['status' => BorrowingStatus::OVERDUE]);
        }

        return $borrowingIds->count();
    }
}
