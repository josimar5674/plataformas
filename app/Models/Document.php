<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [

        'nombre',
        'archivo',
        'tipo',
        'descripcion'

    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function documentos()
{
    return $this->morphMany(
        Document::class,
        'documentable'
    );
}


}
