<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'configuration_option_id',
        'invoice_number',
        'invoice_date',
        'amount',
        'provider_name',
        'image_path',
        'description',
        'reviewed',
        'received_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'amount' => 'decimal:2',
            'reviewed' => 'boolean',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(
            ConfigurationOption::class,
            'configuration_option_id'
        );
    }
}