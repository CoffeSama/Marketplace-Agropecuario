@extends('layouts.adminlte')

@section('title', 'Mis productos')
@section('page_title', 'Mis productos')

@section('content')

@if(auth()->user()->isProductor() && auth()->user()->estaVerificado())
    <a href="{{ route('productos.create') }}" class="btn btn-success mb-3">
        Agregar producto
    </a>
@else
    <div class="alert alert-warning">
        Tu cuenta debe estar verificada para publicar productos.
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($productos->count() > 0)
            <div class="row">
                @foreach($productos as $producto)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            @if($producto->imagenes->first())
                                <img src="{{ asset('storage/' . $producto->imagenes->first()->ruta) }}"
                                     class="card-img-top"
                                     style="height: 180px; object-fit: cover;">
                            @endif

                            <div class="card-body">
                                <h5>{{ $producto->nombre }}</h5>
                                <p><strong>Categoría:</strong> {{ $producto->categoria }}</p>
                                <p><strong>Precio:</strong> Bs {{ $producto->precio }}</p>
                                <p>
                                    <strong>Cantidad:</strong>
                                    {{ $producto->cantidad_disponible }} {{ $producto->unidad_medida }}
                                </p>
                                <p>{{ $producto->descripcion }}</p>

                                <span class="badge {{ $producto->estado_disponibilidad === 'preventa' ? 'badge-warning' : 'badge-success' }}">
                                    {{ $producto->estado_disponibilidad === 'preventa' ? 'Preventa' : 'Disponible' }}
                                </span>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <a href="{{ route('productos.edit', $producto) }}"
                                   class="btn btn-sm btn-outline-primary btn-block">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Aún no tienes productos publicados.</p>
        @endif
    </div>
</div>

@endsection