<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Procedure;
use App\Models\Treatment;
use App\Models\Dentist;

class TreatmentProcedure extends Model
{
    protected $fillable = ['execution_date', 'dentist_id', 'treatment_id', 'procedure_id'];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
}
