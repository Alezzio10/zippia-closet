<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Marca;

class MarcaController extends Controller
{

    public function index()
    {
        try{

            $marcas = Marca::orderBy('id','desc')->get();

            return response()->json([
                'marcas' => $marcas
            ],200);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al obtener las marcas',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function store(Request $request)
    {
        try{

            $request->validate([
                'nombre_marca' => 'required|string|max:255'
            ]);

            $marca = Marca::create([
                'nombre_marca' => $request->nombre_marca
            ]);

            return response()->json([
                'message' => 'Marca creada correctamente',
                'marca' => $marca
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

            $marca = Marca::findOrFail($id);

            return response()->json([
                'marca' => $marca
            ],200);

        }catch(ModelNotFoundException $e){

            return response()->json([
                'message' => 'Marca no encontrada con ID = '.$id
            ],404);
        }
    }


    public function update(Request $request, string $id)
    {
        try{

            $marca = Marca::findOrFail($id);

            $request->validate([
                'nombre_marca' => 'required|string|max:255'
            ]);

            $marca->update($request->all());

            return response()->json([
                'message' => 'Marca actualizada correctamente',
                'marca' => $marca
            ],200);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error al actualizar la marca',
                'error' => $e->getMessage()
            ],500);
        }
    }


    public function destroy(string $id)
    {
        try{

            $marca = Marca::findOrFail($id);

            $marca->delete();

            return response()->json([
                'message' => 'Marca eliminada correctamente'
            ],200);

        }catch(ModelNotFoundException $e){

            return response()->json([
                'message' => 'Marca no encontrada con ID = '.$id
            ],404);
        }
    }
}