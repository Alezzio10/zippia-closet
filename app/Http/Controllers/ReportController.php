<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function ventas(Request $request)
    {
        try {
            $query = Pedido::with(['detalles.producto.categoria', 'usuario'])
                ->whereHas('detalles')
                ->orderBy('created_at', 'desc');

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('estado') && $request->estado !== 'Todos') {
                $query->where('estado', $request->estado);
            }

            $pedidos = $query->get();

            $registros = [];
            foreach ($pedidos as $p) {
                foreach ($p->detalles as $d) {
                    $registros[] = [
                        'id' => 'P-' . $p->id . '-' . $d->id,
                        'pedido_id' => $p->id,
                        'date' => $p->created_at->format('Y-m-d'),
                        'product' => $d->producto?->nombre ?? 'N/A',
                        'category' => $d->producto?->categoria?->nombre_categoria ?? 'Sin categoría',
                        'quantity' => $d->cantidad,
                        'unitPrice' => (float) $d->precio,
                        'total' => (float) $d->subtotal,
                        'status' => $this->mapEstado($p->estado),
                        'estado' => $p->estado,
                        'client' => $p->usuario?->name ?? 'N/A',
                    ];
                }
            }

            $categories = [];
            $byCategory = [];
            foreach ($registros as $r) {
                $cat = $r['category'];
                $byCategory[$cat] = ($byCategory[$cat] ?? 0) + $r['total'];
            }
            foreach ($byCategory as $name => $value) {
                $categories[] = ['name' => $name, 'value' => round($value, 2)];
            }

            $totalSales = array_sum(array_column($registros, 'total'));
            $pedidosUnicos = collect($registros)->pluck('pedido_id')->unique()->count();
            $avgTicket = $pedidosUnicos > 0 ? $totalSales / $pedidosUnicos : 0;
            $byProduct = [];
            foreach ($registros as $r) {
                $byProduct[$r['product']] = ($byProduct[$r['product']] ?? 0) + $r['total'];
            }
            arsort($byProduct);
            $topProduct = array_key_first($byProduct) ?? '—';

            return response()->json([
                'registros' => $registros,
                'summary' => [
                    'totalVentas' => round($totalSales, 2),
                    'totalPedidos' => $pedidosUnicos,
                    'ticketPromedio' => round($avgTicket, 2),
                    'productoTop' => $topProduct,
                ],
                'porCategoria' => array_values($categories),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function mapEstado(string $estado): string
    {
        return match (strtoupper($estado)) {
            'PAGADO', 'ENTREGADO' => 'completado',
            'PENDIENTE', 'ENVIADO' => 'pendiente',
            'CANCELADO' => 'cancelado',
            default => 'pendiente',
        };
    }
}
