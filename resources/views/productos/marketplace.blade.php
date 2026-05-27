@extends('layouts.adminlte')

@section('title', 'Marketplace')
@section('page_title', 'Marketplace Agropecuario')

@section('content')

    <div class="row">
        @forelse($productos as $producto)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    @if ($producto->imagenes->first())
                        <img src="{{ asset('storage/' . $producto->imagenes->first()->ruta) }}" class="card-img-top"
                            style="height: 200px; object-fit: cover;">
                    @endif

                    <div class="card-body">
                        <h5>{{ $producto->nombre }}</h5>

                        <p>
                            <strong>Categoría:</strong>
                            {{ $producto->categoria }}
                        </p>

                        <p>
                            <strong>Precio:</strong>
                            Bs {{ number_format($producto->precio, 2) }}
                        </p>

                        <p>
                            <strong>Disponible:</strong>
                            {{ $producto->cantidad_disponible }} {{ $producto->unidad_medida }}
                        </p>

                        <p>
                            {{ $producto->descripcion }}
                        </p>

                        @if ($producto->estado_disponibilidad === 'preventa')
                            <span class="badge badge-warning">
                                Preventa disponible
                            </span>

                            @php
                                $diasDisponibles = max(
                                    0,
                                    (int) ceil(now()->diffInDays($producto->fecha_disponibilidad, false)),
                                );
                            @endphp

                            <p class="mt-2">
                                <strong>Disponible en:</strong>
                                {{ $diasDisponibles }} {{ $diasDisponibles === 1 ? 'día' : 'días' }}
                            </p>

                            <p>
                                <strong>Fecha de disponibilidad:</strong>
                                {{ $producto->fecha_disponibilidad->format('d/m/Y') }}
                            </p>

                            @if (auth()->user()->isComprador())
                                <form action="{{ route('preventas.store', $producto) }}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label>Cantidad a reservar</label>
                                        <input type="number" step="0.01" min="0.01"
                                            max="{{ $producto->cantidad_disponible }}" name="cantidad" class="form-control"
                                            required>
                                    </div>

                                    <button type="submit" class="btn btn-warning btn-block">
                                        Reservar con anticipo 40%
                                    </button>
                                </form>
                            @endif
                        @else
                            <span class="badge badge-success">
                                Disponible ahora
                            </span>
                        @endif

                        <hr>

                        <small class="text-muted">
                            Productor: {{ $producto->productor->name ?? 'No disponible' }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Todavía no hay productos publicados en el marketplace.
                </div>
            </div>
        @endforelse
    </div>

    {{ $productos->links() }}

@endsection
