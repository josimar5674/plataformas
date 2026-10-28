<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceIncome extends Model
{
    protected $fillable = [
        'business_customer_id',
        'description',
        'income_date',
        'subtotal_usd',
        'finance_tax_rate_id',
        'tax_name_snapshot',
        'tax_percentage_snapshot',
        'tax_amount_usd',
        'total_usd',
        'exchange_rate',
        'subtotal_hnl',
        'tax_amount_hnl',
        'total_hnl',
        'status',
        'notes',
        'created_by',
        'copied_from_income_id',
    ];

    protected $casts = [
        'income_date' => 'date',
        'subtotal_usd' => 'decimal:2',
        'tax_percentage_snapshot' => 'decimal:4',
        'tax_amount_usd' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'subtotal_hnl' => 'decimal:2',
        'tax_amount_hnl' => 'decimal:2',
        'total_hnl' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(BusinessCustomer::class, 'business_customer_id');
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(FinanceTaxRate::class, 'finance_tax_rate_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FinancePayment::class);
    }

    public function getPaidHnlAttribute(): string
    {
        $paid = $this->payments_sum_amount_hnl
            ?? $this->payments()->sum('amount_hnl');

        return number_format((float) $paid, 2, '.', '');
    }

    public function getOutstandingHnlAttribute(): string
    {
        $paid = (float) ($this->payments_sum_amount_hnl
            ?? $this->payments()->sum('amount_hnl'));

        return number_format(max((float) $this->total_hnl - $paid, 0), 2, '.', '');
    }
}
