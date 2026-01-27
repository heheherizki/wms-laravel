<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah kolom status agar menerima 'partial'
        // Kita gunakan Raw SQL karena mengubah ENUM via Eloquent kadang butuh package tambahan
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid', 'partial', 'paid', 'cancelled') NOT NULL DEFAULT 'unpaid'");
    }

    public function down()
    {
        // Kembalikan ke semula jika rollback (hati-hati data partial bisa error jika di rollback)
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid', 'paid', 'cancelled') NOT NULL DEFAULT 'unpaid'");
    }
};