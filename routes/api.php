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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Ruta para gestionar el estado de el pedido (solo admin)
Route::patch('/pedidos/estado/{id}', [PedidoController::class, 'gestionarEstado'])->middleware(['auth:api', 'admin']);
Route::get('/pedidos/mis-pedidos', [PedidoController::class, 'misPedidosPagados']);
Route::get('/pedidos/mis-pedidos-todos', [PedidoController::class, 'misPedidos']);
//Rutas de la API
Route::post('users/register', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);
Route::get('productos', [ProductoController::class, 'index']);
Route::get('productos/{id}', [ProductoController::class, 'show']);
Route::post('productos', [ProductoController::class, 'store'])->middleware(['auth:api', 'admin']);
Route::put('productos/{id}', [ProductoController::class, 'update'])->middleware(['auth:api', 'admin']);
Route::delete('productos/{id}', [ProductoController::class, 'destroy'])->middleware(['auth:api', 'admin']);
Route::get('categorias', [CategoriaController::class, 'index']);
Route::get('categorias/{id}', [CategoriaController::class, 'show']);
Route::post('categorias', [CategoriaController::class, 'store'])->middleware(['auth:api', 'admin']);
Route::put('categorias/{id}', [CategoriaController::class, 'update'])->middleware(['auth:api', 'admin']);
Route::delete('categorias/{id}', [CategoriaController::class, 'destroy'])->middleware(['auth:api', 'admin']);
Route::get('marcas', [MarcaController::class, 'index']);
Route::get('marcas/{id}', [MarcaController::class, 'show']);
Route::post('marcas', [MarcaController::class, 'store'])->middleware(['auth:api', 'admin']);
Route::put('marcas/{id}', [MarcaController::class, 'update'])->middleware(['auth:api', 'admin']);
Route::delete('marcas/{id}', [MarcaController::class, 'destroy'])->middleware(['auth:api', 'admin']);
Route::get('roles', [RolController::class, 'index'])->middleware(['auth:api', 'admin']);
Route::post('pedidos', [PedidoController::class, 'store'])->middleware('auth:api');
Route::delete('pedidos/{id}', [PedidoController::class, 'destroy'])->middleware(['auth:api', 'admin']);
Route::apiResource('pedidos', PedidoController::class)->except(['store', 'destroy']);
Route::apiResource('direcciones', DireccionController::class);
Route::get('users', [UserController::class, 'index'])->middleware(['auth:api', 'admin']);
Route::get('users/{id}', [UserController::class, 'show'])->middleware(['auth:api', 'admin']);
Route::post('users', [UserController::class, 'store'])->middleware(['auth:api', 'admin']);
Route::put('users/{id}', [UserController::class, 'update'])->middleware(['auth:api', 'admin']);
Route::delete('users/{id}', [UserController::class, 'destroy'])->middleware(['auth:api', 'admin']);
//ruta de metodo pago
Route::apiResource('metodo-pagos', MetodoPagoController::class);
Route::post('metodo-pagos/{metodoId}/probar-pago', [MetodoPagoController::class, 'probarPago']);
Route::apiResource('pagos', PagoController::class);
Route::post('pagos/{pagoId}/pagar', [PagoController::class, 'pagar']);
// Webhook Wompi
Route::post('/webhook/wompi', [WebhookController::class, 'wompi'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Tokenización Wompi
Route::post('/tokenizar', [WompiController::class, 'tokenizar']);

// Reporte (solo admin)
Route::get('report/ventas', [ReportController::class, 'ventas'])->middleware(['auth:api', 'admin']);
//Rutas para AuthController
Route::prefix('auth')->group(function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('admin-login', [AuthController::class, 'adminLogin']);

    Route::middleware('auth:api')->group(function(){
        Route::get('me',[AuthController::class, 'me']);
        Route::post('logout',[AuthController::class, 'logout']);
        Route::post('refresh',[AuthController::class, 'refresh']);
    });
});
