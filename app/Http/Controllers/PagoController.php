<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagoController extends Controller
{
    public function pagar(Request $request, string $id)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $pago = Pago::findOrFail($id);

            if ((int) $pago->user_id !== (int) $data['user_id']) {
                return response()->json([
                    'message' => 'No autorizado para pagar este pago.',
                ], 403);
            }

            // En este punto es donde normalmente se llamaría a Wompi para crear/confirmar la transacción.
            // Por ahora dejamos el estado coherente para que el front pueda continuar el flujo de prueba.
            $pago->estado = 'Procesando';
            $pago->save();

            return response()->json([
                'message' => 'Pago enviado a procesamiento.',
                'pago' => $pago,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pago no encontrado con id = ' . $id,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el pago',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

        $request = Pago::orderBy('id','asc')->get();

        return response()->json([
            'Pagos: ' => $request
        ],200);

    }catch(\Exception $e){

        return response()->json([
            'message' => 'error al obtener los pagos',
            'error' => $e->getMessage()
        ],500);
        }
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
        try{

            $data = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'fechaPago' => 'required|date',
            'metodo_id' => 'required|exists:metodo_pagos,id'
            ]);
            DB::beginTransaction();

            $pago = Pago::create([
                'pedido_id' => $data['pedido_id'],
                'fechaPago' => $data['fechaPago'],
                'metodo_id' => $data['metodo_id']
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pago creado correctamente',
                'data' => $pago
            ],201);

        }catch(ValidationException $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Error de validación.',
                'errores' => $e->errors()
            ], 422);

        }catch(\Exception $e){

            DB::rollBack();
            

            return response()->json([
                'message' => 'Error al crear el pago',
                'error' => $e->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
        $pago = Pago::with(['pedido', 'metodoPago'])->findOrFail($id);

        return response()->json([
            'message' => 'Pago encontrado correctamente',
            'data' => $pago
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Pago no encontrado con id = ' . $id
        ], 404);

    } catch (\Exception $e) {
        // Cualquier otro error
        return response()->json([
            'message' => 'Error al obtener el pago',
            'error' => $e->getMessage()
        ], 500);
    }
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
            try {
            $pago = Pago::findOrFail($id);

            // Validaciones
            $data = $request->validate([
                'pedido_id' => 'required|exists:pedidos,id',
                'fechaPago' => 'required|date',
                'metodo_id' => 'required|exists:metodo_pagos,id'
            ]);


            DB::beginTransaction();

            // Actualizamos los campos del pago
            $pago->update([
                'pedido_id' => $data['pedido_id'],
                'fechaPago' => $data['fechaPago'],
                'metodo_id' => $data['metodo_id']
            ]);

            // se confirma todo
            DB::commit();

            return response()->json([
                'message' => 'Pago actualizado correctamente',
                'data' => $pago
            ], 202);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Pago no encontrado con id = ' . $id
            ], 404);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el pago',
                'error' => $e->getMessage()
            ], 500);
        }
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

        $pago = Pago::findOrFail($id);


        $pago->delete();

        return response()->json([
            'message' => 'Pago eliminado correctamente'
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Pago no encontrado con id = ' . $id
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al eliminar el pago',
            'error' => $e->getMessage()
        ], 500);
    }
    }
}
