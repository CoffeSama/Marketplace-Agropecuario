@extends('layouts.adminlte')

@section('title', 'Mis preventas')
@section('page_title', 'Mis preventas')

@section('content')

    <div class="card">
        <div class="card-body">
            @forelse($preventas as $preventa)
                <div class="border rounded p-3 mb-3">
                    <h5>{{ $preventa->producto->nombre }}</h5>

                    <p><strong>Cantidad:</strong> {{ $preventa->cantidad }}</p>
                    <p><strong>Total:</strong> Bs {{ number_format($preventa->total, 2) }}</p>
                    <p><strong>Anticipo 40%:</strong> Bs {{ number_format($preventa->anticipo, 2) }}</p>
                    <p><strong>Saldo pendiente:</strong> Bs {{ number_format($preventa->saldo, 2) }}</p>
                    <p><strong>Fecha de disponibilidad:</strong> {{ $preventa->fecha_disponibilidad->format('d/m/Y') }}</p>

                    @if (!$preventa->anticipo_pagado)
                        <span class="badge badge-danger">Pendiente de anticipo</span>
                    @elseif ($preventa->estado === 'pendiente_saldo')
                        <span class="badge badge-warning">Pendiente de saldo</span>
                    @elseif($preventa->estado === 'completado')
                        <span class="badge badge-success">Completado</span>
                    @endif

                    @if (!$preventa->anticipo_pagado)
                        <div class="alert alert-warning mt-3">
                            Debes pagar el anticipo del 40% para confirmar esta preventa.
                        </div>

                        <a href="{{ route('preventas.pagar-anticipo', $preventa) }}" class="btn btn-primary">
                            <i class="fas fa-qrcode mr-1"></i> Pagar anticipo con QR
                        </a>
                    @elseif (
                        ($preventa->fecha_disponibilidad->isPast() || $preventa->fecha_disponibilidad->isToday()) &&
                            $preventa->estado === 'pendiente_saldo')
                        <div class="alert alert-info mt-3">
                            La cosecha ya está disponible. Debes completar el saldo de la compra.
                        </div>

                        <a href="{{ route('preventas.pagar-saldo', $preventa) }}" class="btn btn-success">
                            <i class="fas fa-qrcode mr-1"></i> Pagar saldo con QR
                        </a>
                    @elseif($preventa->estado === 'pendiente_saldo')
                        @php
                            $diasDisponibles = max(
                                0,
                                (int) ceil(now()->diffInDays($preventa->fecha_disponibilidad, false)),
                            );
                        @endphp

                        <div class="alert alert-secondary mt-3">
                            La cosecha estará disponible en {{ $diasDisponibles }}
                            {{ $diasDisponibles === 1 ? 'día' : 'días' }}.
                        </div>
                    @endif
                </div>
            @empty
                <p>No tienes preventas registradas.</p>
            @endforelse
        </div>
    </div>

@endsection
