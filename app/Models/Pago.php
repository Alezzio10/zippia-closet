<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'pedido_id',
        'fechaPago',
        'metodo_id'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'carrito_id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_id');
    }
}
