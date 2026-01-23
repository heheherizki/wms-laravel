<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = ['shipment_number', 'sales_order_id', 'user_id', 'date', 'notes'];

    // Relasi ke Sales Order Induk
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    // Relasi ke Detail Barang yang dikirim
    public function details()
    {
        return $this->hasMany(ShipmentDetail::class);
    }
    
    // Relasi ke User pembuat
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}