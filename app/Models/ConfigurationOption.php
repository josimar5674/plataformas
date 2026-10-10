<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
class ConfigurationOption extends Model
{
    protected $fillable = [

        'catalog_id',

        'name',

        'description',

        'active',

        'sort_order',

    ];


    protected $casts = [

        'active' => 'boolean',

        'sort_order' => 'integer',

    ];


    /*
    |--------------------------------------------------------------------------
    | CATÁLOGO AL QUE PERTENECE
    |--------------------------------------------------------------------------
    */

    public function catalog()
    {
        return $this->belongsTo(
            ConfigurationCatalog::class,
            'catalog_id'
        );
    }

    public function invoices()
{
    return $this->hasMany(Invoice::class);
}
}