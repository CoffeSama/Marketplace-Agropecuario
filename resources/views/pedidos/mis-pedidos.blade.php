@extends('layouts.adminlte')

@section('title', 'Mis pedidos')
@section('page_title', 'Mis pedidos')

@section('content')

@if($pedidos->isEmpty())
    <div class="card">
        <div class="card-body text-center">
            <h4>Aún no tienes pedidos</h4>
            <p>Confirma tu carrito desde el marketplace para generar un pedido.</p>
            <a href="{{ route('productos.marketplace') }}" class="btn btn-primary">Ir al Marketplace</a>
        </div>
    </div>
@else
    @foreach($pedidos as $pedido)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    Pedido #{{ $pedido->id }}
                    <small class="text-muted ml-2">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                </h3>
                @php
                    $badges = [
                        'pendiente' => ['badge-warning', 'Pendiente de confirmación'],
                        'aceptado'  => ['badge-success', 'Aceptado'],
                        'rechazado' => ['badge-danger', 'Rechazado'],
                        'pagado'    => ['badge-primary', 'Pagado'],
                    ];
                    [$clase, $texto] = $badges[$pedido->estado];
                @endphp
                <span class="badge {{ $clase }}">{{ $texto }}</span>
            </div>

            <div class="card-body">
                <p class="mb-2">
                    <i class="fas fa-user mr-1 text-muted"></i>
                    <strong>Productor:</strong> {{ $pedido->productor->name ?? '—' }}
                </p>

                <table class="table table-sm table-bordered mb-3">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->items as $item)
                            <tr>
                                <td>{{ $item->nombre_producto }}</td>
                                <td>{{ $item->cantidad }} {{ $item->unidad_medida }}</td>
                                <td>Bs {{ number_format($item->precio_unitario, 2) }}</td>
                                <td>Bs {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Total: Bs {{ number_format($pedido->total, 2) }}</h5>

                    @if($pedido->estado === 'pendiente')
                        <span class="text-muted small">
                            <i class="fas fa-clock mr-1"></i> Esperando respuesta del productor
                        </span>
                    @elseif($pedido->estado === 'aceptado')
                        <a href="{{ route('pedidos.pagar', $pedido) }}" class="btn btn-primary">
                            <i class="fas fa-qrcode mr-1"></i> Pagar con QR
                        </a>
                    @elseif($pedido->estado === 'rechazado')
                        <span class="text-danger small">
                            <i class="fas fa-times-circle mr-1"></i> El productor rechazó este pedido
                        </span>
                    @else
                        <span class="text-success small">
                            <i class="fas fa-check-circle mr-1"></i> Pago completado
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection
