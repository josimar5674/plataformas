<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_name',
        'platform',
        'is_active',
        'registered_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'registered_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Usuario propietario del dispositivo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comprueba si el dispositivo está autorizado.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}