<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
     protected $table = 'pedidos';

    protected $fillable = [
        'usuario_id',
        'total',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleProducto::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
