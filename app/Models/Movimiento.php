<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $fillable = [
        'expediente_id',
        'fecha',
        'descripcion',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

}