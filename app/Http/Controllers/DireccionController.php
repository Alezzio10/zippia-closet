<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Direccion;

class DireccionController extends Controller
{
    /**
     * Listar todas las direcciones
     */
    public function index()
    {
        return response()->json(Direccion::all());
    }

    /**
     * Guardar una nueva dirección
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'municipio' => 'required|string|max:255',
            'calle' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
        ]);

        $direccion = Direccion::create($validated);

        return response()->json($direccion, 201);
    }

    /**
     * Mostrar una dirección específica
     */
    public function show(string $id)
    {
        $direccion = Direccion::findOrFail($id);

        return response()->json($direccion);
    }

    /**
     * Actualizar una dirección
     */
    public function update(Request $request, string $id)
    {
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
     * Eliminar una dirección
     */
    public function destroy(string $id)
    {
        $direccion = Direccion::findOrFail($id);
        $direccion->delete();

        return response()->json([
            "mensaje" => "Dirección eliminada correctamente"
        ]);
    }
}