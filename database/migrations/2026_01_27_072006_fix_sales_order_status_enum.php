<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Kita perbarui kolom status agar menerima 'on_hold'
        // Gunakan Raw SQL agar kompatibel dengan MySQL
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN status ENUM('pending', 'on_hold', 'partial', 'shipped', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Kembalikan ke asal (hati-hati data on_hold bisa error jika rollback)
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN status ENUM('pending', 'partial', 'shipped', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};