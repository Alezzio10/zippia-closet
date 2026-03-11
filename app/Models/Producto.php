<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
     protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'talla',
        'color',
        'imagen',
        'marca_id',
        'categoria_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleProducto::class);
    }
    
    public function imagenes()
    {
    return $this->hasMany(Imagen::class);
    }
}
//
