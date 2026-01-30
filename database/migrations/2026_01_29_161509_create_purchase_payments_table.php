<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            // Relasi ke PO
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            
            // Data Pembayaran
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // Cash, Transfer, Giro
            $table->string('reference_number')->nullable(); // No Ref Bank / No Cek
            $table->text('notes')->nullable();
            
            // Siapa yang input (Audit Trail)
            $table->foreignId('user_id')->constrained('users'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_payments');
    }
};