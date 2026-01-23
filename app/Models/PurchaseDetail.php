<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'buy_price', 'subtotal'
    ];

    // Relasi Balik: Detail milik PO
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Relasi: Detail adalah Produk tertentu
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}