<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Procedure;
use App\Models\PriceTable;
use App\Models\CashAccount;
use App\Models\Patient;
// use App\Models\Dentist;
use App\Models\Payment;
use App\Models\CashierSession;

class Budget extends Model
{
    protected $fillable = [
        'value',
        'unit_value',
        'date',
        'notes',
        'status',
        'guide',
        'teeth_region',
        'dentition',
        'cash_id',
        'cashier_session_id',
        'registration_dentist',
        'procedure_dentist',
        'procedure_id',
        'execute_all_procedures',
        'patient_id',
        'price_table_id',
        'treatment_id',
    ];

    public function procedure(){
        return $this->belongsTo(Procedure::class);
    }

    public function priceTable(){
        return $this->belongsTo(PriceTable::class);
    }

    public function cashAccount(){
        return $this->belongsTo(CashAccount::class, 'cash_id');
    }

    public function cashierSession()
    {
        return $this->belongsTo(CashierSession::class, 'cashier_session_id');
    }

    public function patient(){
        return $this->belongsTo(Patient::class);
    }

    // public function dentist(){
    //     return $this->belongsTo(Dentist::class);
    // }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
