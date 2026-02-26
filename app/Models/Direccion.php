<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
      protected $table = 'direcciones';

    protected $fillable = [
        'municipio',
        'calle',
        'departamento'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
