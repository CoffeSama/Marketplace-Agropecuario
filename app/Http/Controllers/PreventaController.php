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

        $preventa = DB::transaction(function () use ($producto, $validated, $user) {
            $total = $producto->precio * $validated['cantidad'];
            $anticipo = $total * 0.40;
            $saldo = $total - $anticipo;

            $preventa = Preventa::create([
                'producto_id' => $producto->id,
                'comprador_id' => $user->id,
                'cantidad' => $validated['cantidad'],
                'total' => $total,
                'anticipo' => $anticipo,
                'anticipo_pagado' => false,
                'saldo' => $saldo,
                'saldo_pagado' => false,
                'estado' => 'pendiente_saldo',
                'fecha_disponibilidad' => $producto->fecha_disponibilidad,
            ]);

            $producto->cantidad_disponible -= $validated['cantidad'];
            $producto->save();

            return $preventa;
        });

        return redirect()->route('preventas.pagar-anticipo', $preventa)
            ->with('success', 'Preventa registrada correctamente. Ahora simula el pago QR del anticipo del 40%.');
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

    public function mostrarPagoAnticipo(Preventa $preventa)
    {
        $this->validarComprador($preventa);

        if ($preventa->anticipo_pagado) {
            return redirect()->route('mis-preventas')
                ->with('info', 'El anticipo de esta preventa ya fue pagado.');
        }

        return $this->mostrarPagoQr($preventa, 'anticipo');
    }

    public function confirmarPagoAnticipo(Preventa $preventa)
    {
        $this->validarComprador($preventa);

        if ($preventa->anticipo_pagado) {
            return redirect()->route('mis-preventas')
                ->with('info', 'El anticipo de esta preventa ya fue pagado.');
        }

        $preventa->update(['anticipo_pagado' => true]);

        return redirect()->route('mis-preventas')
            ->with('success', 'Pago QR simulado del anticipo registrado correctamente.');
    }

    public function mostrarPagoSaldo(Preventa $preventa)
    {
        $this->validarComprador($preventa);

        if (!$preventa->anticipo_pagado) {
            return redirect()->route('preventas.pagar-anticipo', $preventa)
                ->with('error', 'Primero debes pagar el anticipo de la preventa.');
        }

        if ($preventa->fecha_disponibilidad->isFuture()) {
            return redirect()->route('mis-preventas')
                ->with('error', 'Aún no se puede completar el pago. La cosecha todavía no está disponible.');
        }

        if ($preventa->saldo_pagado || $preventa->estado === 'completado') {
            return redirect()->route('mis-preventas')
                ->with('info', 'Esta preventa ya fue completada anteriormente.');
        }

        return $this->mostrarPagoQr($preventa, 'saldo');
    }

    public function confirmarPagoSaldo(Preventa $preventa)
    {
        $this->validarComprador($preventa);

        if (!$preventa->anticipo_pagado) {
            return redirect()->route('preventas.pagar-anticipo', $preventa)
                ->with('error', 'Primero debes pagar el anticipo de la preventa.');
        }

        if ($preventa->fecha_disponibilidad->isFuture()) {
            return redirect()->route('mis-preventas')
                ->with('error', 'Aún no se puede completar el pago. La cosecha todavía no está disponible.');
        }

        if ($preventa->saldo_pagado || $preventa->estado === 'completado') {
            return redirect()->route('mis-preventas')
                ->with('info', 'Esta preventa ya fue completada anteriormente.');
        }

        $preventa->update([
            'estado' => 'completado',
            'saldo' => 0,
            'saldo_pagado' => true,
        ]);

        return redirect()->route('mis-preventas')
            ->with('success', 'Pago QR simulado del saldo registrado correctamente. La compra fue finalizada.');
    }

    private function validarComprador(Preventa $preventa): void
    {
        abort_unless($preventa->comprador_id === Auth::id(), 403);
    }

    private function mostrarPagoQr(Preventa $preventa, string $tipo)
    {
        $preventa->load(['producto.productor', 'comprador']);

        $monto = $tipo === 'anticipo' ? $preventa->anticipo : $preventa->saldo;
        $titulo = $tipo === 'anticipo' ? 'Pago QR del anticipo' : 'Pago QR del saldo';
        $descripcion = $tipo === 'anticipo'
            ? 'Anticipo del 40% para reservar la preventa.'
            : 'Saldo final de la preventa, disponible cuando la cosecha ya puede entregarse.';
        $rutaConfirmacion = $tipo === 'anticipo'
            ? route('preventas.pagar-anticipo.confirmar', $preventa)
            : route('preventas.pagar-saldo.confirmar', $preventa);
        $qrPayload = 'AGROVIDA|PREVENTA:' . $preventa->id . '|TIPO:' . strtoupper($tipo) . '|MONTO:' . $monto;

        return view('preventas.pago-qr', compact(
            'preventa',
            'tipo',
            'monto',
            'titulo',
            'descripcion',
            'rutaConfirmacion',
            'qrPayload'
        ));
    }
}
