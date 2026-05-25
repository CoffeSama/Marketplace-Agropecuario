@extends('layouts.adminlte')

@section('title', 'Marketplace')
@section('page_title', 'Marketplace Agropecuario')

@section('content')

<div class="row">
    @forelse($productos as $producto)
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                @if($producto->imagenes->first())
                    <img src="{{ asset('storage/' . $producto->imagenes->first()->ruta) }}"
                         class="card-img-top"
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
                        Bs {{ $producto->precio }}
                    </p>

                    <p>
                        <strong>Disponible:</strong>
                        {{ $producto->cantidad_disponible }} {{ $producto->unidad_medida }}
                    </p>

                    <p>
                        {{ $producto->descripcion }}
                    </p>

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