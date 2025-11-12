<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Budget;
use App\Models\CashAccount;

class CurrentAccount extends Model
{
    protected $table = 'current_account';

    protected $fillable = [
        'budget_id',
        'cash_id',
        'cashier_session_id',
        'payment_method',
        'amount',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function cashAccount()
    {
        return $this->hasMany(CashAccount::class);
    }

    public function cashierSession()
    {
        return $this->hasMany(CashierSession::class);
    }
}
