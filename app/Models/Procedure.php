<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Treatment;
use App\Models\PriceTable;
use App\Models\Budget;

class Procedure extends Model
{
    protected $fillable = [
        'name'
    ];

    public function priceTable()
    {
        return $this->belongsToMany(PriceTable::class, 'price_table_procedure')
                    ->withPivot('value');
    }

    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'treatment_procedure')
                    ->withPivot('execution_date', 'dentist_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
