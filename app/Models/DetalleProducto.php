<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleProducto extends Model
{
     protected $table = 'detalle_productos';

    protected $fillable = [
        'producto_id',
        'pedido_id',
        'cantidad',
        'talla',
        'precio',
        'subtotal'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
