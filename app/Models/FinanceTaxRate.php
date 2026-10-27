<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTaxRate extends Model
{
    protected $fillable = [
        'name',
        'percentage',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
