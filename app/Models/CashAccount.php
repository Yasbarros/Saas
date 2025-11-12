<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Budget;
use App\Models\Procedure;
use App\Models\Patient;
use App\Models\CurrentAccount;
use App\Models\CashierSession;
use Carbon\Carbon;

class CashAccount extends Model
{
    protected $table = 'cash_accounts';
    protected $fillable = [
        'initial_balance',
        'name',
        'status',
        'user_id'
    ];

    public function cashierSessions(){
        return $this->hasMany(CashierSession::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function budgets(){
        return $this->hasMany(Budget::class);
    }

    public function procedures(){
        return $this->hasMany(Procedure::class);
    }

    public function patients(){
        return $this->hasMany(Patient::class);
    }
}
