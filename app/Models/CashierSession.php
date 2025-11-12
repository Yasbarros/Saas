<?php
namespace App\Models;
use App\Models\User;
use App\Models\CashAccount;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CashierSession extends Model
{
    protected $fillable = [
        'user_id_opened',
        'user_id_closed',
        'cash_account_id',
        'closed_at',
        'opened_at',
        'initial_balance',
        'final_balance',
        'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userOpened()
    {
        return $this->belongsTo(User::class, 'user_id_opened');
    }

    public function userClosed()
    {
        return $this->belongsTo(User::class, 'user_id_closed');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'cashier_session_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function getTotalCashEntriesAttribute()
    {
        return $this->payments->flatMap(function ($payment) {
            return $payment->paymentMethods->where('method', 'cash');
        })->sum('amount');
    }

    public function getTotalCreditCardEntriesAttribute()
    {
        return $this->payments->flatMap(function ($payment) {
            return $payment->paymentMethods->where('method', 'credit_card');
        })->sum('amount');
    }

    public function getTotalDebitCardEntriesAttribute()
    {
        return $this->payments->flatMap(function ($payment) {
            return $payment->paymentMethods->where('method', 'debit_card');
        })->sum('amount');
    }

    public function getTotalPixEntriesAttribute()
    {
        return $this->payments->flatMap(function ($payment) {
            return $payment->paymentMethods->where('method', 'pix');
        })->sum('amount');
    }

    public function getTotalOtherEntriesAttribute()
    {
        return $this->payments->flatMap(function ($payment) {
            return $payment->paymentMethods->where('method', 'other');
        })->sum('amount');
    }

    public function getTotalEntriesAttribute()
    {
        return $this->getTotalCashEntriesAttribute() +
               $this->getTotalCreditCardEntriesAttribute() +
               $this->getTotalDebitCardEntriesAttribute() +
               $this->getTotalPixEntriesAttribute() +
               $this->getTotalOtherEntriesAttribute();
    }

    public function getTotalExitsAttribute()
    {
        // TODO: Implementar lógica para saídas quando houver
        return 0.00;
    }

    public function getFinalBalanceAttribute()
    {
        return (float)$this->initial_balance + $this->getTotalEntriesAttribute() - $this->getTotalExitsAttribute();
    }

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->opened_at)->format('d/m/Y H:i');
    }

    public function getStatusAttribute($value)
    {
        return $this->closed_at ? 0 : 1;
    }
}

