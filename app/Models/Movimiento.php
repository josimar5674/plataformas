<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Document;

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

    public function documentos()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}