<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;

class WebhookController extends Controller
{
    public function wompi(Request $request)
{
    Log::info('HEADERS', $request->headers->all());
    Log::info('RAW BODY', ['body' => $request->getContent()]);
    Log::info('ALL()', $request->all());

    return response()->json([
        'message' => 'webhook recibido'
    ], 200);
}

}
