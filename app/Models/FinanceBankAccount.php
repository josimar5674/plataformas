<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceBankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'account_name',
        'account_number',
        'currency',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(FinancePayment::class, 'finance_bank_account_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->bank_name . ' — ' . $this->account_name . ' (' . $this->account_number . ')';
    }
}
