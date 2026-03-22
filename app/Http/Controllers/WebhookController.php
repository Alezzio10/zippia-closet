<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;

class WebhookController extends Controller
{
    public function wompi(Request $request)
{
    \Log::info('🔥 WEBHOOK Wompi', $request->all());

    $transaction = $request->input('data.transaction');

    if (!$transaction) {
        return response()->json(['ok' => true], 200);
    }

    $status = $transaction['status'] ?? null;
    $reference = $transaction['reference'] ?? null;

    if (!$reference) {
        return response()->json(['ok' => true], 200);
    }

    // Ejemplo: pedido_5 → 5
    $pagoId = str_replace('pedido_', '', $reference);

    $pago = \App\Models\Pago::with('pedido')->find($pagoId);

    if (!$pago) {
        return response()->json(['ok' => true], 200);
    }

    $nuevoEstado = ($status === 'APPROVED') ? 'Completado' : 'Fallida';

    $pago->estado = $nuevoEstado;
    $pago->save();

    if ($pago->pedido) {
        $pago->pedido->estado = ($nuevoEstado === 'Completado') ? 'PAGADO' : 'PENDIENTE';
        $pago->pedido->save();
    }

    return response()->json(['ok' => true], 200);
}
}


