<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_return_id',
        'product_id',
        'quantity',
        'condition', // bagus/rusak (opsional, jika ada di migration)
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}