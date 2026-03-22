<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;

class WebhookController extends Controller
{
    public function wompi(Request $request)
{
    $raw = $request->getContent();

    Log::info('RAW BODY: ' . $raw);

    return response()->json([
        'message' => 'Webhook recibido'
    ], 200);
}

}
