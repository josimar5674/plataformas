<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceIncomeItem extends Model
{
    protected $fillable = [
        'finance_income_id',
        'comercial_id',
        'product_name_snapshot',
        'quantity',
        'unit_price_usd',
        'subtotal_usd',
        'finance_tax_rate_id',
        'tax_name_snapshot',
        'tax_percentage_snapshot',
        'tax_amount_usd',
        'total_usd',
        'subtotal_hnl',
        'tax_amount_hnl',
        'total_hnl',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price_usd' => 'decimal:2',
        'subtotal_usd' => 'decimal:2',
        'tax_percentage_snapshot' => 'decimal:4',
        'tax_amount_usd' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'subtotal_hnl' => 'decimal:2',
        'tax_amount_hnl' => 'decimal:2',
        'total_hnl' => 'decimal:2',
    ];

    public function income(): BelongsTo
    {
        return $this->belongsTo(FinanceIncome::class, 'finance_income_id');
    }

    public function comercial(): BelongsTo
    {
        return $this->belongsTo(Comercial::class, 'comercial_id');
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(FinanceTaxRate::class, 'finance_tax_rate_id');
    }
}
