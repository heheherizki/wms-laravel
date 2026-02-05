<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use HasFactory;
    
    // Pastikan nama tabel benar
    protected $table = 'cash_accounts';

    protected $fillable = ['name', 'account_number', 'balance', 'description'];

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class)->latest();
    }
}