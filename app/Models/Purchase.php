<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
    'po_number',
    'supplier_id',
    'user_id',
    'date',
    'notes',
    'status',
    'payment_status',
    'amount_paid',
    'total_amount', // <--- Gunakan ini
    ];

    // Relasi
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    // Relasi ke history pembayaran
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class)->orderBy('date', 'desc');
    }
}