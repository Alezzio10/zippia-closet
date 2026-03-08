<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Categoria;

class CategoriaController extends Controller
{

    public function index()
    {
        try{

            $categorias = Categoria::orderBy('id','desc')->get();

            return response()->json([
                'categorias' => $categorias
            ],200);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al obtener las categorias',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function store(Request $request)
    {
        try{

            $request->validate([
                'nombre_categoria' => 'required|string|max:255',
                'descripcion_categoria' => 'nullable|string'
            ]);

            $categoria = Categoria::create([
                'nombre_categoria' => $request->nombre_categoria,
                'descripcion_categoria' => $request->descripcion_categoria
            ]);

            return response()->json([
                'message' => 'Categoria creada correctamente',
                'categoria' => $categoria
            ],201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ],422);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function show(string $id)
    {
        try{

            $categoria = Categoria::findOrFail($id);

            return response()->json([
                'categoria' => $categoria
            ]);

        }catch(ModelNotFoundException $e){

            return response()->json([
                'message' => 'Categoria no encontrada con ID = '.$id
            ],404);
        }
    }


    public function update(Request $request, string $id)
    {
        try{

            $categoria = Categoria::findOrFail($id);

            $request->validate([
                'nombre_categoria' => 'required|string|max:255',
                'descripcion_categoria' => 'nullable|string'
            ]);

            $categoria->update($request->all());

            return response()->json([
                'message' => 'Categoria actualizada correctamente',
                'categoria' => $categoria
            ],200);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al actualizar la categoria',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function destroy(string $id)
    {
        try{

            $categoria = Categoria::findOrFail($id);

            $categoria->delete();

            return response()->json([
                'message' => 'Categoria eliminada correctamente'
            ],200);

        }catch(ModelNotFoundException $e){

            return response()->json([
                'message' => 'Categoria no encontrada con ID = '.$id
            ],404);
        }
    }
}