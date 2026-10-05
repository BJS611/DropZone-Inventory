<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Lab Jaringan', 'code' => 'LAB-JAR', 'description' => 'Laboratorium jaringan'],
            ['name' => 'Lab Multimedia', 'code' => 'LAB-MM', 'description' => 'Laboratorium multimedia'],
            ['name' => 'Gudang', 'code' => 'GDG', 'description' => 'Gudang utama'],
            ['name' => 'Ruang Admin', 'code' => 'R-ADM', 'description' => 'Ruang administrasi'],
            ['name' => 'Server Room', 'code' => 'SRV', 'description' => 'Ruang server'],
        ];

        foreach ($locations as $row) {
            Location::firstOrCreate(['code' => $row['code']], $row);
        }
    }
}
