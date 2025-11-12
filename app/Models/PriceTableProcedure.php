<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PriceTable;
use App\Models\Procedure;

class PriceTableProcedure extends Model
{
    protected $fillable = ['value', 'price_table_id', 'procedure_id'];

    public function priceTable()
    {
        return $this->belongsTo(PriceTable::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
