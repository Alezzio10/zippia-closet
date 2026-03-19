<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Pedido;
use App\Models\DetalleProducto;
use App\Models\Producto;

class PedidoController extends Controller
{
    public function misPedidosPagados(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $pedidos = Pedido::with([
                'detalles.producto.marca',
                'pagos' => function ($query) {
                    $query->whereIn('estado', ['Completado', 'Exitoso', 'Pagado'])->orderByDesc('id');
                },
            ])
                ->where('usuario_id', (int) $data['user_id'])
                ->where(function ($query) {
                    $query->where('estado', 'PAGADO')
                        ->orWhereHas('pagos', function ($pagoQuery) {
                            $pagoQuery->whereIn('estado', ['Completado', 'Exitoso', 'Pagado']);
                        });
                })
                ->whereHas('detalles')
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'pedidos' => $pedidos,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pedidos pagados',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // LISTAR PEDIDOS
    public function index()
    {
        try {

            $pedidos = Pedido::with(['usuario','detalles.producto','pagos'])
                ->orderBy('id','desc')
                ->get();
            return response()->json([
                'pedidos' => $pedidos
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pedidos',
                'error' => $e->getMessage()
            ],500);
        }
    }
    // CREAR PEDIDO
    public function store(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|exists:users,id',
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1'
            ]);
            $total = 0;
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $total += $producto->precio * $item['cantidad'];
            }
            // crear pedido
            $pedido = Pedido::create([
                'usuario_id' => $request->usuario_id,
                'total' => $total,
                'estado' => 'PENDIENTE'
            ]);
            // crear detalles
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                DetalleProducto::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio' => $producto->precio,
                    'subtotal' => $producto->precio * $item['cantidad']
                ]);
            }
            return response()->json([
                'message' => 'Pedido creado correctamente',
                'pedido' => $pedido->load('detalles.producto')
            ],201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ],422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el pedido',
                'error' => $e->getMessage()
            ],500);
        }
    }

    // MOSTRAR PEDIDO
    public function show(string $id)
    {
        try {
            $pedido = Pedido::with(['usuario','detalles.producto','pagos'])->findOrFail($id);
            return response()->json([
                'pedido' => $pedido
            ],200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pedido no encontrado con ID = '.$id
            ],404);
        }
    }
    // ACTUALIZAR ESTADO DEL PEDIDO
    public function update(Request $request, string $id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            $request->validate([
                'estado' => 'required|in:PENDIENTE,PAGADO,CANCELADO,ENVIADO,ENTREGADO'
            ]);
            $pedido->update([
                'estado' => $request->estado
            ]);
            return response()->json([
                'message' => 'Estado del pedido actualizado',
                'pedido' => $pedido
            ],200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ],422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el pedido',
                'error' => $e->getMessage()
            ],500);
        }
    }
    // ELIMINAR PEDIDO
    public function destroy(string $id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            $pedido->delete();
            return response()->json([
                'message' => 'Pedido eliminado correctamente'
            ],200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pedido no encontrado con ID = '.$id
            ],404);
        }
    }
    public function gestionarEstado(Request $request, $id)
{
    try {

        // Buscar pedido
        $pedido = Pedido::findOrFail($id);

        // Validar estado
        $data = $request->validate([
            'estado' => 'required|in:PENDIENTE,PAGADO,CANCELADO,ENVIADO,ENTREGADO'
        ]);

        // Actualizar estado
        $pedido->estado = $data['estado'];
        $pedido->save();

        return response()->json([
            'message' => 'Estado del pedido actualizado correctamente',
            'pedido' => $pedido
        ],200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

        return response()->json([
            'message' => 'Pedido no encontrado con ID = '.$id
        ],404);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'message' => 'Error de validación',
            'errores' => $e->errors()
        ],422);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Error al gestionar el estado del pedido',
            'error' => $e->getMessage()
        ],500);
    }
}
}

