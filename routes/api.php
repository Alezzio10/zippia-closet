<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WompiController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Ruta para gestionar el estado de el pedido
Route::patch('/pedidos/estado/{id}', [PedidoController::class, 'gestionarEstado']);
Route::get('/pedidos/mis-pedidos', [PedidoController::class, 'misPedidosPagados']);
//Rutas de la API
Route::post('users/register', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('marcas', MarcaController::class);
Route::apiResource('pedidos', PedidoController::class);
Route::apiResource('direcciones', DireccionController::class);
Route::apiResource('users', UserController::class);
//ruta de metodo pago
Route::apiResource('metodo-pagos', MetodoPagoController::class);
Route::post('metodo-pagos/{metodoId}/probar-pago', [MetodoPagoController::class, 'probarPago']);
Route::apiResource('pagos', PagoController::class);
Route::post('pagos/{pagoId}/pagar', [PagoController::class, 'pagar']);
// Webhook Wompi
Route::post('/webhook/wompi', [WebhookController::class, 'wompi']);

// Tokenización Wompi
Route::post('/tokenizar', [WompiController::class, 'tokenizar']);
//Rutas para AuthController
Route::prefix('auth')->group(function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function(){
        Route::get('me',[AuthController::class, 'me']);
        Route::post('logout',[AuthController::class, 'logout']);
        Route::post('refresh',[AuthController::class, 'refresh']);
    });
});
