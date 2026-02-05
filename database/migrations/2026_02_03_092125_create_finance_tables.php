<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Akun Keuangan (Dompet)
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Bank BCA, Kas Kecil
            $table->string('account_number')->nullable(); // No Rekening
            $table->decimal('balance', 15, 2)->default(0); // Saldo Saat Ini
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Kategori Biaya (Agar laporan rapi)
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Listrik & Air, Gaji, Transportasi
            $table->timestamps();
        });

        // 3. Tabel Transaksi Kas (Log Masuk/Keluar)
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts'); // Dari dompet mana?
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories'); // Untuk apa? (Jika expense)
            $table->foreignId('user_id')->constrained('users'); // Siapa yang input?
            
            $table->date('date');
            $table->enum('type', ['in', 'out', 'transfer']); // Masuk, Keluar, Pindah Buku
            $table->decimal('amount', 15, 2);
            $table->string('description'); // Keterangan
            $table->string('reference_id')->nullable(); // No Bukti / Ref
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('cash_accounts');
    }
};