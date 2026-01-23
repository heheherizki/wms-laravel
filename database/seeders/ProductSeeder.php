<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'sku' => 'LMP-001-9W-WHT',
            'name' => 'LED Bulb Premium',
            'brand' => 'Stark',
            'category' => 'Lampu Indoor',
            'watt' => '9 Watt',
            'color' => 'Putih (6500K)',
            'stock' => 525, 
            'min_stock' => 50,
            'unit' => 'Pcs',
            'pack_unit' => 'Dus',
            'pack_quantity' => 50, 
            'rack_location' => 'A-01-01',
        ]);

        Product::create([
            'sku' => 'LMP-002-12W-YEL',
            'name' => 'LED Bulb Eco',
            'brand' => 'Stark',
            'category' => 'Lampu Indoor',
            'watt' => '12 Watt',
            'color' => 'Kuning (3000K)',
            'stock' => 48, 
            'min_stock' => 50,
            'unit' => 'Pcs',
            'pack_unit' => 'Dus',
            'pack_quantity' => 50,
            'rack_location' => 'A-01-02',
        ]);

        Product::create([
            'sku' => 'FL-001-50W',
            'name' => 'Floodlight Sorot Outdoor',
            'brand' => 'Tiger',
            'category' => 'Lampu Outdoor',
            'watt' => '50 Watt',
            'color' => 'Putih (6500K)',
            'stock' => 115, 
            'min_stock' => 10,
            'unit' => 'Pcs',
            'pack_unit' => 'Dus',
            'pack_quantity' => 10, 
            'rack_location' => 'B-05-01',
        ]);

        Product::create([
            'sku' => 'STR-005-HD',
            'name' => 'Senter Kepala High Power',
            'brand' => 'Lion',
            'category' => 'Senter',
            'watt' => '15 Watt',
            'color' => 'Putih',
            'stock' => 240, 
            'min_stock' => 20,
            'unit' => 'Pcs',
            'pack_unit' => 'Dus',
            'pack_quantity' => 60, 
            'rack_location' => 'C-02-01',
        ]);
        
        Product::create([
            'sku' => 'ACC-SC-01',
            'name' => 'Stop Kontak 4 Lubang + Kabel',
            'brand' => 'Tiger',
            'category' => 'Aksesoris',
            'watt' => null, 
            'color' => 'Putih',
            'stock' => 500, 
            'min_stock' => 100,
            'unit' => 'Pcs',
            'pack_unit' => 'Dus',
            'pack_quantity' => 40,
            'rack_location' => 'D-10-05',
        ]);
    }
}