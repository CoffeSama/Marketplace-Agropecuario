<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function index()
    {
        $carrito = Carrito::with([
            'items.producto',
            'productor',
        ])
            ->where('comprador_id', Auth::id())
            ->where('estado', 'activo')
            ->first();

        $total = $carrito ? $carrito->items->sum('subtotal') : 0;

        return view('carrito.index', compact('carrito', 'total'));
    }

    public function agregar(Request $request, Producto $producto)
    {
        $request->validate([
            'cantidad' => ['required', 'numeric', 'min:0.01'],
        ]);

        $cantidad = (float) $request->cantidad;

        // Validar que el producto esté publicado
        if ($producto->estado !== 'publicado') {
            return back()->with('error', 'Este producto no está disponible para agregar al carrito.');
        }

        // Validar stock disponible
        if ($cantidad > (float) $producto->cantidad_disponible) {
            return back()->with('error', 'La cantidad solicitada supera la cantidad disponible.');
        }

        // En este proyecto el productor del producto es el usuario dueño del producto
        $productorId = $producto->user_id;

        // Buscar o crear carrito activo del comprador
        $carrito = Carrito::firstOrCreate(
            [
                'comprador_id' => Auth::id(),
                'estado' => 'activo',
            ],
            [
                'productor_id' => null,
            ]
        );

        // Validar que el carrito solo tenga productos de un productor
        if ($carrito->productor_id !== null && $carrito->productor_id != $productorId) {
            return back()->with('error', 'Solo puedes agregar productos de un mismo productor por pedido.');
        }

        // Si el carrito aún no tiene productor, asignarlo
        if ($carrito->productor_id === null) {
            $carrito->update([
                'productor_id' => $productorId,
            ]);
        }

        // Buscar si el producto ya existe en el carrito
        $item = $carrito->items()
            ->where('producto_id', $producto->id)
            ->first();

        // Si ya existe, se suma la cantidad
        $nuevaCantidad = $item
            ? (float) $item->cantidad + $cantidad
            : $cantidad;

        // Validar que la nueva cantidad no supere el stock
        if ($nuevaCantidad > (float) $producto->cantidad_disponible) {
            return back()->with('error', 'No puedes agregar más cantidad que la disponible.');
        }

        // Actualizar o crear item del carrito
        if ($item) {
            $item->update([
                'cantidad' => $nuevaCantidad,
                'subtotal' => $nuevaCantidad * (float) $item->precio_unitario,
            ]);
        } else {
            $carrito->items()->create([
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
                'subtotal' => $cantidad * (float) $producto->precio,
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito correctamente.');
    }

    public function actualizar(Request $request, Producto $producto)
    {
        $request->validate([
            'cantidad' => ['required', 'numeric', 'min:0.01'],
        ]);

        $cantidad = (float) $request->cantidad;

        $carrito = Carrito::where('comprador_id', Auth::id())
            ->where('estado', 'activo')
            ->firstOrFail();

        $item = $carrito->items()
            ->where('producto_id', $producto->id)
            ->firstOrFail();

        // Validar stock disponible
        if ($cantidad > (float) $producto->cantidad_disponible) {
            return back()->with('error', 'La cantidad solicitada supera la cantidad disponible.');
        }

        $item->update([
            'cantidad' => $cantidad,
            'subtotal' => $cantidad * (float) $item->precio_unitario,
        ]);

        return back()->with('success', 'Cantidad actualizada correctamente.');
    }

    public function eliminar(Producto $producto)
    {
        $carrito = Carrito::where('comprador_id', Auth::id())
            ->where('estado', 'activo')
            ->firstOrFail();

        $item = $carrito->items()
            ->where('producto_id', $producto->id)
            ->first();

        if ($item) {
            $item->delete();
        }

        // Si el carrito quedó vacío, se limpia el productor asignado
        if ($carrito->items()->count() === 0) {
            $carrito->update([
                'productor_id' => null,
            ]);
        }

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar()
    {
        $carrito = Carrito::where('comprador_id', Auth::id())
            ->where('estado', 'activo')
            ->first();

        if ($carrito) {
            $carrito->items()->delete();

            $carrito->update([
                'productor_id' => null,
            ]);
        }

        return redirect()
            ->route('carrito.index')
            ->with('success', 'Carrito vaciado correctamente.');
    }
}
