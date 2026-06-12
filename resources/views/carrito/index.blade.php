@extends('layouts.adminlte')

@section('title', 'Carrito de Compras')
@section('page_title', 'Carrito de Compras')

@section('content')



@if(!$carrito || $carrito->items->count() === 0)
    <div class="card">
        <div class="card-body text-center">
            <h4>Tu carrito está vacío</h4>
            <p>Agrega productos desde el marketplace para iniciar tu compra.</p>

            <a href="{{ route('productos.marketplace') }}" class="btn btn-primary">
                Volver al Marketplace
            </a>
        </div>
    </div>
@else

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Productos seleccionados
            </h3>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                Este carrito solo permite productos de un mismo productor por pedido.
                <br>
                <strong>Productor:</strong> {{ $carrito->productor->name ?? 'No disponible' }}
            </div>

            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Unidad</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th width="170">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($carrito->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->producto->nombre }}</strong>
                                <br>
                                <small>{{ $item->producto->categoria }}</small>
                            </td>

                            <td>
                                {{ $item->producto->unidad_medida }}
                            </td>

                            <td>
                                Bs {{ number_format($item->precio_unitario, 2) }}
                            </td>

                            <td>
                                <form action="{{ route('carrito.actualizar', $item->producto_id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <input 
                                        type="number"
                                        name="cantidad"
                                        value="{{ $item->cantidad }}"
                                        min="0.01"
                                        step="0.01"
                                        max="{{ $item->producto->cantidad_disponible }}"
                                        class="form-control"
                                        style="width: 100px;"
                                        required
                                    >

                                    <button type="submit" class="btn btn-warning btn-sm">
                                        Actualizar
                                    </button>
                                </form>

                                <small class="text-muted">
                                    Disponible: {{ $item->producto->cantidad_disponible }} {{ $item->producto->unidad_medida }}
                                </small>
                            </td>

                            <td>
                                Bs {{ number_format($item->subtotal, 2) }}
                            </td>

                            <td>
                                <form action="{{ route('carrito.eliminar', $item->producto_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <h3>Total: Bs {{ number_format($total, 2) }}</h3>
                </div>

                <div class="d-flex gap-2">
                    <form action="{{ route('carrito.vaciar') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-danger">
                            Vaciar carrito
                        </button>
                    </form>

                    <form action="{{ route('pedidos.confirmar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i> Confirmar pedido
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <a href="{{ route('productos.marketplace') }}" class="btn btn-secondary mt-3">
        Seguir comprando
    </a>

@endif

@endsection