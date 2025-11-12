<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Budget;

class PriceTable extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    public function procedures(){
        return $this->belongsToMany(Procedure::class, 'price_table_procedure')
                    ->withPivot('value');
    }
    public function budgets(){
        return $this->hasMany(Budget::class);
    }
}
