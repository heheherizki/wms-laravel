<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah tipe kolom payment_terms menjadi VARCHAR(50) agar bisa menampung teks "NET 60", "Cash", dll.
        DB::statement("ALTER TABLE customers MODIFY COLUMN payment_terms VARCHAR(50) NULL DEFAULT NULL");
    }

    public function down()
    {
        // Kembalikan ke Integer jika di-rollback (Hati-hati data teks akan hilang/error)
        // Kita gunakan DB statement change kolom biasa, tapi resiko data loss tinggi disini.
        // Sebaiknya biarkan kosong atau ubah logic sesuai kebutuhan backup.
        DB::statement("ALTER TABLE customers MODIFY COLUMN payment_terms INT NULL DEFAULT NULL");
    }
};