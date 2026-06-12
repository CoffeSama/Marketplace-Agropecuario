@extends('layouts.adminlte')

@section('title', 'Ventas futuras')
@section('page_title', 'Ventas futuras')

@section('content')

<div class="card">
    <div class="card-body">
        @forelse($preventas as $preventa)
            <div class="border rounded p-3 mb-3">
                <h5>{{ $preventa->producto->nombre }}</h5>

                <p><strong>Comprador:</strong> {{ $preventa->comprador->name }}</p>
                <p><strong>Cantidad reservada:</strong> {{ $preventa->cantidad }}</p>
                <p><strong>Total venta:</strong> Bs {{ number_format($preventa->total, 2) }}</p>
                <p><strong>Anticipo 40%:</strong> Bs {{ number_format($preventa->anticipo, 2) }}</p>
                <p><strong>Saldo pendiente:</strong> Bs {{ number_format($preventa->saldo, 2) }}</p>
                <p><strong>Fecha de disponibilidad:</strong> {{ $preventa->fecha_disponibilidad->format('d/m/Y') }}</p>

                @if(!$preventa->anticipo_pagado)
                    <span class="badge badge-danger">Anticipo pendiente</span>
                @elseif($preventa->estado === 'pendiente_saldo')
                    <span class="badge badge-warning">Pendiente de saldo</span>
                @elseif($preventa->estado === 'completado')
                    <span class="badge badge-success">Venta completada</span>
                @endif

                @if($preventa->anticipo_pagado)
                    <span class="badge badge-info ml-1">Anticipo recibido</span>
                @endif
            </div>
        @empty
            <p>Aún no tienes ventas futuras registradas.</p>
        @endforelse
    </div>
</div>

@endsection
