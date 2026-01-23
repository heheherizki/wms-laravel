<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <--- PASTIKAN BARIS INI ADA
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus user lama biar gak duplikat (opsional jika pakai migrate:fresh)
        // User::truncate(); 

        User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@gudang.com',
            'role' => 'admin',
            'password' => Hash::make('password123'), // <--- HARUS PAKAI Hash::make()
        ]);
        
        User::create([
            'name' => 'Staff Logistik',
            'email' => 'staff@gudang.com',
            'role' => 'staff',
            'password' => Hash::make('password123'),
        ]);
    }
}