<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CashAccount;
use App\Models\PriceTable;

class Clinic extends Model
{
    protected $fillable = [
        'name',
        'CNPJ',
        'phone',
        'status',
        'opening_hours',
    ];

    public function cashAccount(){
        return $this->hasMany(CashAccount::class);
    }

    public function priceTables(){
        return $this->hasMany(PriceTable::class);
    }
}
