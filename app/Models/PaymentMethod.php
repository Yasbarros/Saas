<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payments;

class PaymentMethod extends Model
{
    protected $fillable = [
        'payment_id',
        'method',
        'amount',
        'installments'
    ];

    public function payment()
    {
        return $this->belongsTo(Payments::class, 'payment_id');
    }


}
