<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pago;
use App\Services\WompiAuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagoController extends Controller
{
    public function pagar(Request $request, string $pagoId, WompiAuthService $wompiAuth)
    {
        try {
            //Valido datos que llegan a la solicitud
            $data = $request->validate([
                'user_id' => 'sometimes|integer|exists:users,id',
            ]);

            $pago = Pago::with(['pedido', 'metodoPago', 'user'])->findOrFail($pagoId);

            if (isset($data['user_id']) && (int)$pago->user_id !== (int)$data['user_id']) {
                return response()->json([
                    'message' => 'El pago no corresponde al usuario indicado',
                ], 403);
            }

            if (in_array($pago->estado, ['Completado', 'Cancelado'], true)) {
                return response()->json([
                    'message' => 'El pago no se puede procesar en su estado actual',
                    'estado' => $pago->estado,
                ], 409);
            }

            if (!$pago->metodoPago) {
                return response()->json([
                    'message' => 'El pago no tiene método de pago asociado',
                ], 422);
            }

            if (!$pago->metodoPago->token_tarjeta) {
                return response()->json([
                    'message' => 'El método de pago no tiene token de tarjeta (token_tarjeta)',
                ], 422);
            }

            if (!$pago->user) {
                return response()->json([
                    'message' => 'El pago no tiene usuario asociado (user_id)',
                ], 422);
            }

            $pago->estado = 'Procesando';
            $pago->save();

            $idAplicativo = env('WOMPI_APP_ID');

            if (!$idAplicativo) {
                return response()->json([
                    'message' => 'Falta configurar WOMPI_APP_ID en variables de entorno',
                ], 500);
            }

            $token = $wompiAuth->getAccessToken();

            $monto = $pago->pedido?->total;

            if ($monto === null) {
                return response()->json([
                    'message' => 'No se pudo determinar el monto (pedido.total) para el pago',
                ], 422);
            }

            $urlWebhook = rtrim((string) env('APP_URL', ''), '/') . '/api/webhook/wompi';

            $payloadWompi = [
                'monto' => (float) $monto,
                'emailCliente' => (string) $pago->user->email,
                'nombreCliente' => (string) ($pago->user->name ?? ''),
                'tokenTarjeta' => (string) $pago->metodoPago->token_tarjeta,
                'configuracion' => [
                    'emailsNotificacion' => (string) $pago->user->email,
                    'urlWebhook' => $urlWebhook,
                    'telefonosNotificacion' => (string) ($pago->user->telefono ?? ''),
                    'notificarTransaccionCliente' => true,
                ],
                'datosAdicionales' => [
                    'pago_id' => (string) $pago->id,
                    'cliente_id' => (string) $pago->user_id,
                ],
            ];

            $resp = $wompiAuth->http()->baseUrl('https://api.wompi.sv')
                ->acceptJson()
                ->withToken($token)
                ->withQueryParameters(['idAplicativo' => $idAplicativo])
                ->post('/TransaccionCompra/TokenizadaSin3Ds', $payloadWompi);

            if (!$resp->successful()) {
                Log::error('Error iniciando pago tokenizado en Wompi', [
                    'pago_id' => $pago->id,
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                ]);

                $pago->delete();

                return response()->json([
                    'message' => 'No se pudo iniciar la transacción en Wompi',
                    'status' => $resp->status(),
                    'wompi_body' => $resp->json(),
                ], 502);
            }

            // Si Wompi respondió exitosamente, marcamos el pago como completado de inmediato.
            // Esto evita que quede en "Procesando" cuando el webhook no se recibe a tiempo.
            $pago->estado = 'Completado';
            $pago->save();

            if ($pago->pedido) {
                $pago->pedido->estado = 'PAGADO';
                $pago->pedido->save();
            }

            return response()->json([
                'message' => 'Pago exitoso',
                'pago' => $pago->fresh(['pedido', 'metodoPago', 'user']),
                'wompi' => $resp->json(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pago no encontrado con id = ' . $pagoId,
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en /pagos/{pagoId}/pagar', [
                'pago_id' => $pagoId,
                'error' => $e->getMessage(),
            ]);
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

            $pedido = \App\Models\Pedido::findOrFail($data['pedido_id']);
            $pago = Pago::create([
                'pedido_id' => $data['pedido_id'],
                'fechaPago' => $data['fechaPago'],
                'metodo_id' => $data['metodo_id'],
                'user_id' => $pedido->usuario_id,
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
