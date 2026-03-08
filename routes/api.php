<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PagoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Ruta para gestionar el estado de el pedido
Route::patch('/pedidos/estado/{id}', [PedidoController::class, 'gestionarEstado']);
//Rutas de la API
Route::apiResource('productos', ProductoController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('marcas', MarcaController::class);
Route::apiResource('pedidos', PedidoController::class);


//ruta de metodo pago
Route::apiResource('metodo-pagos', MetodoPagoController::class);
Route::apiResource('pagos', PagoController::class);
