<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT Maju Jaya Elektronik', 'contact_name' => 'Budi Santoso', 'phone' => '081234567890', 'email' => 'budi@majujaya.co.id', 'address' => 'Jl. Mangga Dua No. 12, Jakarta'],
            ['name' => 'CV Sumber Komputer', 'contact_name' => 'Siti Aminah', 'phone' => '081298765432', 'email' => 'siti@sumberkomputer.com', 'address' => 'Jl. Diponegoro No. 45, Bandung'],
            ['name' => 'PT Jaringan Nusantara', 'contact_name' => 'Agus Wijaya', 'phone' => '082112345678', 'email' => 'agus@jaranus.id', 'address' => 'Jl. Sudirman No. 88, Surabaya'],
            ['name' => 'UD Furniture Sentosa', 'contact_name' => 'Dewi Lestari', 'phone' => '085788778877', 'email' => 'dewi@furnituresentosa.com', 'address' => 'Jl. Ahmad Yani No. 3, Semarang'],
        ];

        foreach ($suppliers as $row) {
            Supplier::firstOrCreate(['name' => $row['name']], $row);
        }
    }
}
