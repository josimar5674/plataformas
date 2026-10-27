<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceExchangeRate extends Model
{
    protected $fillable = [
        'year',
        'month',
        'rate',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'rate' => 'decimal:4',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }
}
