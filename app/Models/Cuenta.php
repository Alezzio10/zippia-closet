<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
     protected $table = 'cuentas';

    protected $fillable = [
        'nombre',
        'correo',
        'password'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
