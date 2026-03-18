<?php

namespace App\Http\Controllers;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class MetodoPagoController extends Controller
{
    public function probarPago(Request $request, string $metodoId)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'monto' => 'sometimes|numeric|min:0.01',
            ]);

            $metodo = MetodoPago::findOrFail($metodoId);

            if ((int) $metodo->user_id !== (int) $data['user_id']) {
                return response()->json([
                    'message' => 'El método de pago no corresponde al usuario indicado',
                ], 403);
            }

            $monto = isset($data['monto']) ? (float) $data['monto'] : 1.00;

            DB::beginTransaction();

            $pedido = Pedido::create([
                'usuario_id' => (int) $data['user_id'],
                'total' => $monto,
                'estado' => 'PENDIENTE',
            ]);

            $pago = Pago::create([
                'pedido_id' => $pedido->id,
                'metodo_id' => $metodo->id,
                'user_id' => (int) $data['user_id'],
                'fechaPago' => now()->toDateString(),
                'estado' => 'Pendiente',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pago de prueba creado',
                'pedido' => $pedido,
                'pago' => $pago,
            ], 201);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Método de pago no encontrado con id = ' . $metodoId,
            ], 404);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear pago de prueba',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{

        $query = MetodoPago::orderBy('id','asc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $metodos = $query->get();

        return response()->json([
            // compat: mantenemos la llave anterior
            'metodos de pago: ' => $metodos,
            'data' => $metodos,
        ],200);

    }catch(\Exception $e){

        return response()->json([
            'message' => 'error al obtener los metodos de pago',
            'error' => $e->getMessage()
        ],500);

    }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         try{

        //validamos que los datos que vienen de bruno/postman cumplan co estos requisitos y si los cumplen se guardan de data
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cuatro_digitos' => 'required|digits:4',
            'fecha_vencimiento' => 'required|date'
        ]);

        DB::beginTransaction();

        $metodoPago = MetodoPago::create([
            'user_id' => $data['user_id'],
            'cuatro_digitos' => $data['cuatro_digitos'],
            'fecha_vencimiento' => $data['fecha_vencimiento']
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Método de pago creado correctamente',
            'data' => $metodoPago
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
                'message' => 'Error al crear el método de pago',
                'error' => $e->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $metodo = MetodoPago::with('usuario')->findOrFail($id);
            return response()->json($metodo);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'metodo de pago no encontrada, con ID = ' . $id
            ], 404);
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
        try{

        //buscar el metodo de pago
        $metodoPago = MetodoPago::findOrFail($id);

        //validar datos
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cuatro_digitos' => 'required|digits:4',
            'fecha_vencimiento' => 'required|date'
        ]);

        //actualizar registro
        $metodoPago->update($data);

        return response()->json([
            'message' => 'Método de pago actualizado correctamente',
            'data' => $metodoPago
        ],202);

    }catch(\Exception $e){

        return response()->json([
            'message' => 'Error al actualizar el metodo de pago',
            'error' => $e->getMessage()
        ],500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

        $metodoPago = MetodoPago::findOrFail($id);

        $metodoPago->delete();

        return response()->json([
            'message' => 'Método de pago eliminado correctamente'
        ],200);

    } catch (ModelNotFoundException $e) {

        return response()->json([
            'message' => 'metodo de pago no encontrada, con ID = ' . $id
        ],404);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Error al eliminar el método de pago',
            'error' => $e->getMessage()
        ],500);

    }
    }
}
