<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'user_id',
        'date',
        'reason',
        'status', // pending, approved, rejected
        'refund_amount'
    ];

    // Relasi ke Sales Order
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    // Relasi ke User (Admin yang input)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Detail Barang Retur
    public function details()
    {
        return $this->hasMany(SalesReturnDetail::class);
    }
}