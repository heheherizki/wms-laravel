<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status agar menerima 'partial'
        // Kita pakai DB::statement karena Schema::table()->change() untuk ENUM sering bermasalah di Doctrine/DBAL
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN status ENUM('pending', 'partial', 'shipped', 'canceled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Kembalikan ke semula (Hati-hati, data 'partial' bisa error kalau dirollback)
        // Sebaiknya biarkan saja atau update data dulu sebelum rollback
        // DB::statement("UPDATE sales_orders SET status = 'pending' WHERE status = 'partial'");
        // DB::statement("ALTER TABLE sales_orders MODIFY COLUMN status ENUM('pending', 'shipped', 'canceled') NOT NULL DEFAULT 'pending'");
    }
};