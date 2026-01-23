<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number', 'shipment_id', 'sales_order_id', 'date', 'due_date', 'total_amount', 'status'];

    public function shipment() { return $this->belongsTo(Shipment::class); }
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function details() { return $this->hasMany(InvoiceDetail::class); }
}