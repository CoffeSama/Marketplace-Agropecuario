@extends('layouts.adminlte')

@section('title', 'Pedidos recibidos')
@section('page_title', 'Pedidos recibidos')

@section('content')

@php
    $pendientes = $pedidos->where('estado', 'pendiente');
@endphp

@if($pendientes->isNotEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-bell mr-2"></i>
        Tienes <strong>{{ $pendientes->count() }}</strong>
        {{ $pendientes->count() === 1 ? 'pedido pendiente' : 'pedidos pendientes' }} de confirmación.
    </div>
@endif

@if($pedidos->isEmpty())
    <div class="card">
        <div class="card-body text-center">
            <h4>No has recibido pedidos todavía</h4>
            <p>Cuando un comprador confirme su carrito con tus productos, aparecerá aquí.</p>
        </div>
    </div>
@else
    @foreach($pedidos as $pedido)
        <div class="card mb-3 {{ $pedido->estado === 'pendiente' ? 'card-outline card-warning' : '' }}">
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
                    <strong>Comprador:</strong> {{ $pedido->comprador->name ?? '—' }}
                    <span class="text-muted ml-2">{{ $pedido->comprador->email ?? '' }}</span>
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
                        <div class="d-flex" style="gap: 8px;">
                            <form action="{{ route('pedidos.aceptar', $pedido) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check mr-1"></i> Aceptar pedido
                                </button>
                            </form>
                            <form action="{{ route('pedidos.rechazar', $pedido) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-times mr-1"></i> Rechazar pedido
                                </button>
                            </form>
                        </div>
                    @elseif($pedido->estado === 'aceptado')
                        <span class="text-muted small">
                            <i class="fas fa-clock mr-1"></i> Esperando pago del comprador
                        </span>
                    @elseif($pedido->estado === 'pagado')
                        <span class="text-success small">
                            <i class="fas fa-check-circle mr-1"></i> Pedido pagado por el comprador
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection
