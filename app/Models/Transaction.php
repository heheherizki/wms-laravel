<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',      // 'in' atau 'out'
        'quantity',
        'reference', // Supplier / Customer
        'notes',
        'transaction_code',  // Kolom baru (jika sudah migrate)
        'transaction_price', // Kolom baru (jika sudah migrate)
    ];

    // Relasi: Transaksi milik 1 Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi: Transaksi dibuat oleh 1 User (PENTING UNTUK HISTORY)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}