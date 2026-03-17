<?php

namespace App\Services;

use App\Models\WompiAccessToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiAuthService
{
    private const WOMPI_API_BASE = 'https://api.wompi.sv';
    private const WOMPI_ID_BASE = 'https://id.wompi.sv';

    public function http()
    {
        $verify = env('WOMPI_SSL_VERIFY', true);
        $caBundle = env('WOMPI_CA_BUNDLE');

        // En local puedes poner WOMPI_SSL_VERIFY=false si tu entorno rompe SSL.
        // En prod debe quedar en true.
        if ($verify === 'false' || $verify === false || $verify === 0 || $verify === '0') {
            return Http::withOptions(['verify' => false]);
        }

        if ($caBundle) {
            return Http::withOptions(['verify' => $caBundle]);
        }

        return Http::withOptions(['verify' => true]);
    }

    public function getAccessToken(): string
    {
        $ultimo = WompiAccessToken::orderByDesc('id')->first();

        if ($ultimo && $this->isTokenStillUsable($ultimo->access_token, $ultimo->obtained_at?->timestamp, $ultimo->expires_in)) {
            return $ultimo->access_token;
        }

        return $this->refreshToken();
    }

    private function isTokenStillUsable(string $token, ?int $obtainedAtTs, ?int $expiresIn): bool
    {
        // Chequeo "rápido" por tiempo (con margen), y luego validación real con /Aplicativo
        if ($obtainedAtTs && $expiresIn) {
            $now = time();
            $skew = 60; // margen de 1 min
            if (($obtainedAtTs + $expiresIn - $skew) <= $now) {
                return false;
            }
        }

        try {
            $resp = $this->http()->baseUrl(self::WOMPI_API_BASE)
                ->acceptJson()
                ->withToken($token)
                ->get('/Aplicativo');

            return $resp->successful();
        } catch (\Exception $e) {
            Log::warning('Validación de token Wompi falló por excepción', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function refreshToken(): string
    {
        $appId = env('WOMPI_APP_ID');
        $appSecret = env('WOMPI_APP_SECRET');

        if (!$appId || !$appSecret) {
            throw new \RuntimeException('Faltan variables de entorno WOMPI_APP_ID o WOMPI_APP_SECRET');
        }

        $resp = $this->http()->baseUrl(self::WOMPI_ID_BASE)
            ->asForm()
            ->acceptJson()
            ->post('/connect/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'audience' => 'wompi_api',
            ]);

        if (!$resp->successful()) {
            Log::error('No se pudo obtener token de Wompi', [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);
            throw new \RuntimeException('No se pudo autenticar con Wompi');
        }

        $data = $resp->json();

        $token = $data['access_token'] ?? null;

        if (!$token) {
            throw new \RuntimeException('Respuesta de Wompi sin access_token');
        }

        WompiAccessToken::create([
            'access_token' => $token,
            'expires_in' => $data['expires_in'] ?? null,
            'token_type' => $data['token_type'] ?? null,
            'scope' => $data['scope'] ?? null,
            'obtained_at' => now(),
        ]);

        return $token;
    }
}

