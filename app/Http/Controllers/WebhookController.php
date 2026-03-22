<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;

class WebhookController extends Controller
{
    public function wompi(Request $request)
    {
        \Log::info('🔥 WEBHOOK LLEGO', $request->all());
    
        return response()->json(['ok' => true], 200);
    }
}


