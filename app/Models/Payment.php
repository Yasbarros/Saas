<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentMethod;
use App\Models\Budget;
use App\Models\CashAccount;
use App\Models\CashierSession;

class Payment extends Model
{
    protected $fillable = [
        'budget_id',
        'cashier_session_id',
    ];

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'payment_id');
    }
    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
    public function cashierSession()
    {
        return $this->belongsTo(CashierSession::class);
    }
}
