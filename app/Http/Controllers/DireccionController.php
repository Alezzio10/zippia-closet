<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Direccion; // Importar el modelo

class DireccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Listar todas las direcciones
        return response()->json(Direccion::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validar datos
        $validated = $request->validate([
            'municipio' => 'required|string|max:255',
            'calle' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
        ]);

        // Crear nueva dirección
        $direccion = Direccion::create($validated);

        return response()->json($direccion, 201);

        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         // Mostrar una dirección específica
        $direccion = Direccion::findOrFail($id);
        return response()->json($direccion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar y actualizar
        $direccion = Direccion::findOrFail($id);

        $validated = $request->validate([
            'municipio' => 'sometimes|string|max:255',
            'calle' => 'sometimes|string|max:255',
            'departamento' => 'sometimes|string|max:255',
        ]);

        $direccion->update($validated);

        return response()->json($direccion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       // Eliminar
        $direccion = Direccion::findOrFail($id);
        $direccion->delete();

        return response()->json([
            "mensaje" => "Dirección eliminada correctamente"
        ]);
    }
}
