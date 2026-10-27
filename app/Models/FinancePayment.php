<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancePayment extends Model
{
    protected $fillable = [
        'finance_income_id',
        'payment_date',
        'amount_hnl',
        'payment_method',
        'reference',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_hnl' => 'decimal:2',
    ];

    public function income(): BelongsTo
    {
        return $this->belongsTo(FinanceIncome::class, 'finance_income_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
