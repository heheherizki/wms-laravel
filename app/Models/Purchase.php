<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number', 'supplier_id', 'user_id', 'date', 'status', 'total_amount', 'notes'
    ];

    // Relasi: 1 PO punya banyak Detail Barang
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    // Relasi: 1 PO milik 1 Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi: 1 PO dibuat oleh 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}