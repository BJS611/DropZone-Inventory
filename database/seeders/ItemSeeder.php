<?php

namespace Database\Seeders;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Enums\Unit;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@dropzone.test')->first();

        $rows = [
            ['DZ-LP-0001', 'Laptop ThinkPad X1', 'KMP', 'R-ADM', 12, 3, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-MN-0002', 'Monitor Dell 24 inch', 'ELK', 'R-ADM', 20, 5, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-KB-0003', 'Keyboard Mechanical', 'KMP', 'GDG', 35, 10, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-MS-0004', 'Mouse Wireless', 'KMP', 'GDG', 8, 10, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-SW-0005', 'Switch Cisco 24 Port', 'JAR', 'SRV', 4, 2, Unit::UNIT, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-RT-0006', 'Router Mikrotik', 'JAR', 'SRV', 6, 2, Unit::UNIT, ItemCondition::MINOR_DAMAGE, ItemStatus::ACTIVE, 0],
            ['DZ-KB-0007', 'Kabel UTP 50m', 'JAR', 'GDG', 15, 5, Unit::METER, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-PR-0008', 'Printer Epson Laser', 'ELK', 'R-ADM', 3, 2, Unit::UNIT, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-KRS-0009', 'Kursi Ergonomis', 'FRN', 'R-ADM', 10, 4, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-MJA-0010', 'Meja Kerja Besi', 'FRN', 'GDG', 18, 5, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-ATK-0011', 'Pulpen Box 50pcs', 'ATK', 'GDG', 40, 10, Unit::BOX, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-CCT-0012', 'Kamera CCTV Outdoor', 'KMN', 'SRV', 0, 4, Unit::UNIT, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-DVR-0013', 'DVR 16 Channel', 'KMN', 'SRV', 2, 1, Unit::UNIT, ItemCondition::MINOR_DAMAGE, ItemStatus::ACTIVE, 0],
            ['DZ-PRJ-0014', 'Proyektor Epson', 'ELK', 'LAB-MM', 5, 2, Unit::UNIT, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-SPK-0015', 'Speaker Aktif', 'ELK', 'LAB-MM', 9, 3, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-HUB-0016', 'USB Hub 10 Port', 'KMP', 'GDG', 2, 5, Unit::PCS, ItemCondition::DAMAGED, ItemStatus::LOST, 0],
            ['DZ-SCN-0017', 'Scanner Dokumen', 'ELK', 'R-ADM', 4, 2, Unit::UNIT, ItemCondition::GOOD, ItemStatus::INACTIVE, 0],
            ['DZ-TBL-0018', 'Tablet Grafis', 'KMP', 'LAB-MM', 7, 2, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-ACC-0019', 'Akses Card Reader', 'KMN', 'R-ADM', 22, 8, Unit::PCS, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
            ['DZ-BTR-0020', 'UPS 1500VA', 'JAR', 'SRV', 0, 2, Unit::UNIT, ItemCondition::GOOD, ItemStatus::ACTIVE, 0],
        ];

        $audit = app(AuditService::class);

        foreach ($rows as [$sku, $name, $categoryCode, $locationCode, $qty, $min, $unit, $condition, $status, $supplierIdx]) {
            $category = Category::where('code', $categoryCode)->first();
            $location = Location::where('code', $locationCode)->first();
            $supplier = Supplier::inRandomOrder()->first();

            $item = Item::firstOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'description' => 'Barang inventaris '.$name,
                    'category_id' => $category?->id,
                    'location_id' => $location?->id,
                    'supplier_id' => $supplier?->id,
                    'quantity' => $qty,
                    'minimum_stock' => $min,
                    'unit' => $unit,
                    'condition' => $condition,
                    'status' => $status,
                ],
            );

            if ($item->wasRecentlyCreated && $qty > 0) {
                StockTransaction::create([
                    'item_id' => $item->id,
                    'type' => TransactionType::IN,
                    'quantity' => $qty,
                    'to_location_id' => $item->location_id,
                    'note' => 'Stok awal (seeder)',
                    'performed_by' => $admin?->id,
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            $audit->log('item_create', 'Item', $item->id, ['sku' => $sku], $admin);
        }
    }
}
