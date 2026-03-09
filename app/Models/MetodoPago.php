<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
      protected $table = 'metodo_pagos';

    protected $fillable = [
        'user_id',
        'cuatro_digitos',
        'fecha_vencimiento'
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'metodo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
