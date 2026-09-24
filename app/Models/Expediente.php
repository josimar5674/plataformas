<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $fillable = [
        'user_id',
        'numero_expediente',
        'tipo_tramite',
        'matricula',
        'sede',
        'asignado',
        'pretension_principal',
        'cuantia',
        'fecha_presentacion',
        'descripcion_proceso',
        'estado',
        'permite_edicion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sujetos()
    {
        return $this->hasMany(Sujeto::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    // Documentos reutilizables de la plataforma
    public function documentos()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Notas reutilizables de la plataforma
    public function notas()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}