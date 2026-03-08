<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Producto;
use App\Models\Imagen;

class ProductoController extends Controller
{

    public function index()
    {
        try{

            $productos = Producto::with(['categoria','marca','imagenes'])
                        ->orderBy('id','desc')
                        ->get();

            return response()->json([
                'productos' => $productos
            ],200);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function store(Request $request)
    {
        try{

            $request->validate([
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'talla' => 'required|string|max:10',
                'stock' => 'required|integer|min:0',
                'imagen' => 'nullable|image|max:2048'
            ]);

            $producto = Producto::create([
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'talla' => $request->talla,
                'stock' => $request->stock
            ]);

            if ($request->hasFile('imagen')) {

                $archivo = $request->file('imagen');

                $nombreImagen = time().'_'.$archivo->getClientOriginalName();

                $archivo->move(public_path('images'), $nombreImagen);

                Imagen::create([
                    'producto_id' => $producto->id,
                    'nombre' => $nombreImagen,
                    'ruta' => 'images/'.$nombreImagen
                ]);
            }

            return response()->json([
                'message' => 'Producto creado correctamente',
                'producto' => $producto->load('imagenes')
            ],201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ],422);

        } catch(\Exception $e){

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function show(string $id)
    {
        try{

            $producto = Producto::with(['categoria','marca','imagenes'])->findOrFail($id);

            return response()->json([
                'producto' => $producto
            ],200);

        }catch (ModelNotFoundException $e){

            return response()->json([
                'message' => 'Producto no encontrado con ID = '.$id
            ],404);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al obtener el producto',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function update(Request $request, string $id)
    {
        try{

            $producto = Producto::findOrFail($id);

            $request->validate([
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'talla' => 'required|string|max:10',
                'stock' => 'required|integer|min:0',
                'imagen' => 'nullable|image|max:2048'
            ]);

            $producto->update([
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'talla' => $request->talla,
                'stock' => $request->stock
            ]);

            if ($request->hasFile('imagen')) {

                $archivo = $request->file('imagen');

                $nombreImagen = time().'_'.$archivo->getClientOriginalName();

                $archivo->move(public_path('images'), $nombreImagen);

                Imagen::create([
                    'producto_id' => $producto->id,
                    'nombre' => $nombreImagen,
                    'ruta' => 'images/'.$nombreImagen
                ]);
            }

            return response()->json([
                'message' => 'Producto actualizado correctamente',
                'producto' => $producto->load('imagenes')
            ],200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ],422);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function destroy(string $id)
    {
        try{

            $producto = Producto::findOrFail($id);

            $producto->delete();

            return response()->json([
                'message' => 'Producto eliminado correctamente'
            ],200);

        }catch(ModelNotFoundException $e){

            return response()->json([
                'message' => 'Producto no encontrado con ID = '.$id
            ],404);
        }
    }
}