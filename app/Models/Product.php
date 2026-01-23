<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Mendaftarkan kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'sku',
        'name',
        'type',
        'brand',
        'category',
        'watt',
        'color',
        'stock',
        'min_stock',
        'unit',
        'pack_unit',
        'pack_quantity',
        'rack_location',
        'buy_price',
        'sell_price',
        'image',
    ];

    // Helper (Accessor) untuk menampilkan stok dalam format Dus + Pcs
    // Contoh output: "2 Dus (80 Pcs)"
    public function getStockLabelAttribute()
    {
        // Jika tidak ada satuan dus, balikin stok biasa
        if (!$this->pack_quantity || $this->pack_unit == null) {
            return $this->stock . ' ' . $this->unit;
        }

        // Hitung matematika gudang
        $dus = floor($this->stock / $this->pack_quantity); // Pembulatan ke bawah
        $sisaPcs = $this->stock % $this->pack_quantity; // Sisa bagi

        $text = "";
        if ($dus > 0) {
            $text .= $dus . " " . $this->pack_unit;
        }
        
        if ($sisaPcs > 0) {
            if ($dus > 0) $text .= " + ";
            $text .= $sisaPcs . " " . $this->unit;
        }

        if ($this->stock == 0) return "0 " . $this->unit;

        return $text; // Contoh: "5 Dus + 4 Pcs"
    }

    public function getMinStockLabelAttribute()
    {
        // Jika tidak punya satuan pack, atau min_stock 0
        if (!$this->pack_quantity || $this->pack_quantity <= 1 || $this->min_stock == 0) {
            return $this->min_stock . ' ' . $this->unit;
        }

        // Hitung Dus dan Sisa
        $dus = floor($this->min_stock / $this->pack_quantity);
        $sisaPcs = $this->min_stock % $this->pack_quantity;

        $text = "";
        if ($dus > 0) {
            $text .= $dus . " " . $this->pack_unit;
        }
        
        if ($sisaPcs > 0) {
            if ($dus > 0) $text .= " + ";
            $text .= $sisaPcs . " " . $this->unit;
        }

        return $text;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}