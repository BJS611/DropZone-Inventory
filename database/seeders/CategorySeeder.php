<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'code' => 'ELK', 'description' => 'Perangkat elektronik dan gadget'],
            ['name' => 'Komputer', 'code' => 'KMP', 'description' => 'Komputer, laptop, dan aksesoris'],
            ['name' => 'Jaringan', 'code' => 'JAR', 'description' => 'Peralatan jaringan dan kabel'],
            ['name' => 'Furniture', 'code' => 'FRN', 'description' => 'Mebel dan perabot kantor'],
            ['name' => 'Alat Tulis', 'code' => 'ATK', 'description' => 'Alat tulis kantor'],
            ['name' => 'Keamanan', 'code' => 'KMN', 'description' => 'Peralatan keamanan dan pengawasan'],
        ];

        foreach ($categories as $row) {
            Category::firstOrCreate(['code' => $row['code']], $row);
        }
    }
}
