<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiController extends Controller
{
    public function tokenizar(Request $request)
    {
        // Soporta el payload del front (numeroTarjeta/cvv/mesVencimiento/anioVencimiento)
        // y también el formato nativo de Wompi (number/cvc/exp_month/exp_year/card_holder).
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],

            'numeroTarjeta' => ['nullable', 'string'],
            'cvv' => ['nullable', 'string'],
            'mesVencimiento' => ['nullable'],
            'anioVencimiento' => ['nullable'],

            'number' => ['nullable', 'string'],
            'cvc' => ['nullable', 'string'],
            'exp_month' => ['nullable', 'string'],
            'exp_year' => ['nullable', 'string'],
            'card_holder' => ['nullable', 'string'],
        ]);

        $number = $data['number'] ?? $data['numeroTarjeta'] ?? null;
        $cvc = $data['cvc'] ?? $data['cvv'] ?? null;
        $expMonth = $data['exp_month'] ?? $data['mesVencimiento'] ?? null;
        $expYear = $data['exp_year'] ?? $data['anioVencimiento'] ?? null;
        $cardHolder = $data['card_holder'] ?? null;

        if (!$number || !$cvc || !$expMonth || !$expYear) {
            return response()->json([
                'message' => 'Datos incompletos para tokenizar.',
                'errores' => [
                    'number' => ['El número de tarjeta es requerido.'],
                    'cvc' => ['El CVV es requerido.'],
                    'exp_month' => ['El mes de vencimiento es requerido.'],
                    'exp_year' => ['El año de vencimiento es requerido.'],
                ],
            ], 422);
        }

        // Wompi espera exp_year con 2 dígitos (YY). Si viene 4 dígitos (YYYY), tomamos los últimos 2.
        $expYearStr = preg_replace('/\D+/', '', (string) $expYear);
        if (strlen($expYearStr) === 4) {
            $expYearStr = substr($expYearStr, -2);
        }

        // Wompi valida card_holder mínimo 5 caracteres.
        $cardHolderStr = trim((string) ($cardHolder ?: 'Cliente Zippia'));
        if (mb_strlen($cardHolderStr) < 5) {
            $cardHolderStr = 'Cliente Zippia';
        }

        $payload = [
            'number' => preg_replace('/\D+/', '', (string) $number),
            'cvc' => (string) $cvc,
            'exp_month' => str_pad((string) $expMonth, 2, '0', STR_PAD_LEFT),
            'exp_year' => $expYearStr,
            'card_holder' => $cardHolderStr,
        ];

        $publicKey = env('WOMPI_PUBLIC_KEY');
        if (!$publicKey) {
            return response()->json([
                'message' => 'WOMPI_PUBLIC_KEY no está configurada en .env',
            ], 500);
        }

        $baseUrl = rtrim(env('WOMPI_BASE_URL', 'https://sandbox.wompi.co'), '/');
        $verifySsl = filter_var(env('WOMPI_VERIFY_SSL', true), FILTER_VALIDATE_BOOL);

        try {
            $http = Http::withToken($publicKey)
                ->acceptJson()
                ->timeout(30);

            if (!$verifySsl) {
                $http = $http->withoutVerifying();
            }

            $resp = $http->post($baseUrl . '/v1/tokens/cards', $payload);

            if (!$resp->successful()) {
                $json = $resp->json();
                $reason = $json['error']['reason'] ?? null;
                $code = $json['error']['code'] ?? null;

                Log::warning('Wompi tokenización falló', [
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                ]);

                return response()->json([
                    'message' => $reason
                        ? ('No se pudo tokenizar la tarjeta: ' . $reason . ($code ? " ($code)" : ''))
                        : 'No se pudo tokenizar la tarjeta.',
                    'wompi' => $json,
                ], $resp->status());
            }

            return response()->json($resp->json(), 200);
        } catch (\Throwable $e) {
            Log::error('Error tokenizando con Wompi', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error interno tokenizando con Wompi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

