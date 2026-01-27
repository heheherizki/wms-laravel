<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_number', 'customer_id', 'user_id', 'date', 
        'status', 'payment_status', 'grand_total', 'notes'
    ];

    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: 1 SO bisa punya BANYAK Shipment (Partial)
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}