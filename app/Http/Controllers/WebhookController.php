<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;

class WebhookController extends Controller
{
    public function wompi(Request $request)
    {
        try {
            $payload = $request->all();
    
            $pagoId = $payload['Cliente']['pago_id'] ?? null;
            $clienteId = $payload['Cliente']['cliente_id'] ?? null;
            $resultado = $payload['ResultadoTransaccion'] ?? null;
    
            if (!$pagoId || !$clienteId) {
                return response()->json([
                    'message' => 'Datos de cliente incompletos en el webhook',
                ], 400);
            }
    
            $pago = Pago::with('pedido')
                ->where('id', $pagoId)
                ->where('user_id', $clienteId)
                ->first();
    
            if (!$pago) {
                return response()->json([
                    'message' => 'Pago no encontrado para el usuario indicado',
                ], 404);
            }
    
            $nuevoEstado = ($resultado === 'ExitosaAprobada') ? 'Completado' : 'Fallida';
    
            $pago->estado = $nuevoEstado;
            $pago->save();
    
            if ($pago->pedido) {
                $pago->pedido->estado = ($nuevoEstado === 'Completado') ? 'PAGADO' : 'PENDIENTE';
                $pago->pedido->save();
            }
    
            return response()->json([
                'message' => 'Estado de pago actualizado correctamente',
                'pago_id' => $pago->id,
                'estado' => $pago->estado,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el webhook',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
