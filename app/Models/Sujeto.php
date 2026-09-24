<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sujeto extends Model
{
    protected $fillable = [
        'expediente_id',
        'tipo',
        'nombre',
        'identificacion',
        'cah',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function documentos()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function notas()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}