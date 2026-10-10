<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceActivationPin extends Model
{
    protected $fillable = [
        'generated_by',
        'pin_hash',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Administrador que generó el PIN.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Comprueba si el PIN ha expirado.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Comprueba si el PIN ya fue utilizado.
     */
    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /**
     * Comprueba si el PIN todavía puede utilizarse.
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }
}