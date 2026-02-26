<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;




Route::get('/', function () {
    return view('welcome');
   
});
 Route::resource('productos', ProductoController::class);
Route::get('/productos', [ProductoController::class, 'index']);
Route::post('/productos', [ProductosController::class, 'store']);

