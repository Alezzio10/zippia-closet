<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
      protected $table = 'metodo_pagos';

    protected $fillable = [
        'cuatro_digitos',
        'fecha_vencimiento',
        'ccv'
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'metodo_id');
    }
}
