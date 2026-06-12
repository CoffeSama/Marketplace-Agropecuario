<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function confirmar()
    {
        $carrito = Carrito::with('items.producto')
            ->where('comprador_id', Auth::id())
            ->where('estado', 'activo')
            ->first();

        if (!$carrito || $carrito->items->isEmpty()) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío, no se puede generar el pedido.');
        }

        // Validar stock vigente antes de confirmar
        foreach ($carrito->items as $item) {
            if ((float) $item->cantidad > (float) $item->producto->cantidad_disponible) {
                return redirect()->route('carrito.index')
                    ->with('error', "Stock insuficiente para \"{$item->producto->nombre}\". Disponible: {$item->producto->cantidad_disponible} {$item->producto->unidad_medida}.");
            }
        }

        DB::transaction(function () use ($carrito) {
            $pedido = Pedido::create([
                'comprador_id' => $carrito->comprador_id,
                'productor_id' => $carrito->productor_id,
                'total' => $carrito->items->sum('subtotal'),
                'estado' => 'pendiente',
                'visto_productor' => false,
            ]);

            foreach ($carrito->items as $item) {
                $pedido->items()->create([
                    'producto_id' => $item->producto_id,
                    'nombre_producto' => $item->producto->nombre,
                    'unidad_medida' => $item->producto->unidad_medida,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // Limpiar carrito: se cierra como confirmado y se eliminan los items
            $carrito->items()->delete();
            $carrito->update(['estado' => 'confirmado']);
        });

        return redirect()->route('pedidos.mis-pedidos')
            ->with('success', 'Pedido generado correctamente. El productor fue notificado y debe confirmarlo.');
    }

    public function misPedidos()
    {
        $pedidos = Pedido::with(['items', 'productor'])
            ->where('comprador_id', Auth::id())
            ->latest()
            ->get();

        return view('pedidos.mis-pedidos', compact('pedidos'));
    }

    public function recibidos()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden ver pedidos recibidos.');
        }

        $pedidos = Pedido::with(['items', 'comprador'])
            ->where('productor_id', $user->id)
            ->latest()
            ->get();

        // Al abrir el panel, los pedidos pendientes quedan marcados como vistos
        Pedido::where('productor_id', $user->id)
            ->where('visto_productor', false)
            ->update(['visto_productor' => true]);

        return view('pedidos.recibidos', compact('pedidos'));
    }

    public function aceptar(Pedido $pedido)
    {
        if ($pedido->productor_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso sobre este pedido.');
        }

        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $pedido->load('items.producto');

        foreach ($pedido->items as $item) {
            if ((float) $item->cantidad > (float) $item->producto->cantidad_disponible) {
                return back()->with('error', "Stock insuficiente para \"{$item->nombre_producto}\". Actualiza tu producto o rechaza el pedido.");
            }
        }

        DB::transaction(function () use ($pedido) {
            foreach ($pedido->items as $item) {
                $item->producto->decrement('cantidad_disponible', (float) $item->cantidad);
            }
            $pedido->update(['estado' => 'aceptado']);
        });

        return back()->with('success', "Pedido #{$pedido->id} aceptado. El comprador ya puede proceder al pago.");
    }

    public function rechazar(Pedido $pedido)
    {
        if ($pedido->productor_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso sobre este pedido.');
        }

        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $pedido->update(['estado' => 'rechazado']);

        return back()->with('success', "Pedido #{$pedido->id} rechazado.");
    }

    public function pagar(Pedido $pedido)
    {
        if ($pedido->comprador_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso sobre este pedido.');
        }

        if ($pedido->estado !== 'aceptado') {
            return back()->with('error', 'Solo puedes pagar pedidos aceptados por el productor.');
        }

        // Pago simulado (MVP)
        $pedido->update(['estado' => 'pagado']);

        return back()->with('success', "Pago del pedido #{$pedido->id} registrado correctamente (simulado).");
    }
}
