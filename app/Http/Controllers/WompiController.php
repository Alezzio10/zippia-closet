<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use App\Services\WompiAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class WompiController extends Controller
{
    public function tokenizar(Request $request, WompiAuthService $wompiAuth)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'numeroTarjeta' => 'required|string|min:12|max:19',
            'cvv' => 'required|string|min:3|max:4',
            'mesVencimiento' => 'required|integer|min:1|max:12',
            'anioVencimiento' => 'required|integer|min:2000|max:2100',
            'idGrupoTarjetas' => 'sometimes|string',
        ]);

        $userId = (int) $data['user_id'];
        $wompiPayload = Arr::except($data, ['user_id']);

        $token = $wompiAuth->getAccessToken();

        $resp = $wompiAuth->http()->baseUrl('https://api.wompi.sv')
            ->acceptJson()
            ->withToken($token)
            ->post('/Tokenizacion', $wompiPayload);

        if (!$resp->successful()) {
            Log::error('Error tokenizando tarjeta en Wompi', [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);
            return response()->json([
                'message' => 'No se pudo tokenizar la tarjeta',
                'status' => $resp->status(),
            ], 502);
        }

        $json = $resp->json();

        // Wompi no documenta aquí el nombre exacto: intentamos variantes comunes
        $tokenTarjeta =
            $json['token_tarjeta'] ??
            $json['tokenTarjeta'] ??
            $json['TokenTarjeta'] ??
            $json['token'] ??
            $json['Token'] ??
            null;

        if (!$tokenTarjeta) {
            Log::warning('Respuesta de tokenización sin token identificable', [
                'response' => $json,
            ]);
            return response()->json([
                'message' => 'Tokenización exitosa, pero Wompi no devolvió token_tarjeta en un campo esperado',
                'wompi_response' => $json,
            ], 502);
        }

        $last4 = substr(preg_replace('/\D+/', '', $data['numeroTarjeta']), -4);
        $fechaVenc = sprintf('%04d-%02d-01', (int)$data['anioVencimiento'], (int)$data['mesVencimiento']);

        $metodo = MetodoPago::create([
            'user_id' => $userId,
            'cuatro_digitos' => $last4 ?: '0000',
            'fecha_vencimiento' => $fechaVenc,
            'token_tarjeta' => $tokenTarjeta,
        ]);

        return response()->json([
            'message' => 'Tarjeta tokenizada y guardada correctamente',
            'metodo_pago' => $metodo,
            'wompi' => $json,
        ], 201);
    }
}

