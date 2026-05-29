<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Preventa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreventaController extends Controller
{
    public function store(Request $request, Producto $producto)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isComprador()) {
            return redirect()->route('productos.marketplace')
                ->with('error', 'Solo los compradores pueden realizar preventas.');
        }

        if ($producto->estado_disponibilidad !== 'preventa') {
            return redirect()->route('productos.marketplace')
                ->with('error', 'Este producto no está disponible para preventa.');
        }

        $validated = $request->validate([
            'cantidad' => ['required', 'numeric', 'min:0.01', 'max:' . $producto->cantidad_disponible],
        ]);

        DB::transaction(function () use ($producto, $validated, $user) {
            $total = $producto->precio * $validated['cantidad'];
            $anticipo = $total * 0.40;
            $saldo = $total - $anticipo;

            Preventa::create([
                'producto_id' => $producto->id,
                'comprador_id' => $user->id,
                'cantidad' => $validated['cantidad'],
                'total' => $total,
                'anticipo' => $anticipo,
                'saldo' => $saldo,
                'estado' => 'pendiente_saldo',
                'fecha_disponibilidad' => $producto->fecha_disponibilidad,
            ]);

            $producto->cantidad_disponible -= $validated['cantidad'];
            $producto->save();
        });

        return redirect()->route('mis-preventas')
            ->with('success', 'Preventa registrada correctamente. Se pagó el 40% como anticipo.');
    }

    public function misPreventas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $preventas = Preventa::with('producto')
            ->where('comprador_id', $user->id)
            ->latest()
            ->get();

        return view('preventas.mis-preventas', compact('preventas'));
    }

    public function ventasFuturas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden ver ventas futuras.');
        }

        $preventas = Preventa::with(['producto', 'comprador'])
            ->whereHas('producto', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        return view('preventas.ventas-futuras', compact('preventas'));
    }

    public function completarPago(Preventa $preventa)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($preventa->comprador_id !== $user->id) {
            return redirect()->route('mis-preventas')
                ->with('error', 'No puedes completar el pago de esta preventa.');
        }

        if ($preventa->fecha_disponibilidad->isFuture()) {
            return redirect()->route('mis-preventas')
                ->with('error', 'Aún no se puede completar el pago. La cosecha todavía no está disponible.');
        }

        if ($preventa->estado === 'completado') {
            return redirect()->route('mis-preventas')
                ->with('info', 'Esta preventa ya fue completada anteriormente.');
        }

        $preventa->update([
            'estado' => 'completado',
            'saldo' => 0,
        ]);

        return redirect()->route('mis-preventas')
            ->with('success', 'Pago completado correctamente. La compra fue finalizada.');
    }
}
