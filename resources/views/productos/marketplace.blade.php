@extends('layouts.adminlte')

@section('title', 'Marketplace')
@section('page_title', 'Marketplace Agropecuario')

@section('content')

{{-- Panel de filtro por cercanía --}}
<div class="card card-outline card-success mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i> Filtrar por cercanía</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('productos.marketplace') }}" id="filtro-form">
            <input type="hidden" name="lat" id="input-lat" value="{{ request('lat') }}">
            <input type="hidden" name="lng" id="input-lng" value="{{ request('lng') }}">

            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="d-block mb-1"><strong>Mi ubicación</strong></label>
                    <div id="ubicacion-estado" class="text-muted small mb-2">
                        @if(request('lat') && request('lng'))
                            <span class="text-success"><i class="fas fa-check-circle mr-1"></i>
                                Ubicación detectada ({{ number_format(request('lat'),4) }}, {{ number_format(request('lng'),4) }})
                            </span>
                        @else
                            <span><i class="fas fa-info-circle mr-1"></i> Sin ubicación seleccionada</span>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-geolocate">
                        <i class="fas fa-crosshairs mr-1"></i> Detectar mi ubicación
                    </button>
                </div>

                <div class="col-md-4">
                    <label for="radio"><strong>Radio de búsqueda</strong></label>
                    <select name="radio" id="radio" class="form-control">
                        <option value="">-- Todos los productos --</option>
                        @foreach([10, 25, 50, 100] as $km)
                            <option value="{{ $km }}" {{ request('radio') == $km ? 'selected' : '' }}>
                                {{ $km }} km
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mt-2 mt-md-0">
                    <button type="submit" class="btn btn-success btn-block" id="btn-buscar">
                        <i class="fas fa-search mr-1"></i> Buscar
                    </button>
                    @if($filtroActivo)
                        <a href="{{ route('productos.marketplace') }}" class="btn btn-outline-secondary btn-block btn-sm mt-1">
                            <i class="fas fa-times mr-1"></i> Quitar filtro
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($filtroActivo)
            <div class="alert alert-info mt-3 mb-0 py-2">
                <i class="fas fa-filter mr-1"></i>
                Mostrando productos dentro de <strong>{{ $radio }} km</strong> de tu ubicación, ordenados por cercanía.
            </div>
        @endif
    </div>
</div>

{{-- Grid de productos --}}
<div class="row" id="productos-grid">
    @forelse($productos as $producto)
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                @if ($producto->imagenes->first())
                    <img src="{{ asset('storage/' . $producto->imagenes->first()->ruta) }}"
                         class="card-img-top" style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                         style="height: 200px;">
                        <i class="fas fa-seedling fa-3x text-muted"></i>
                    </div>
                @endif

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">{{ $producto->nombre }}</h5>
                        @if($filtroActivo && isset($producto->distancia))
                            <span class="badge badge-success ml-2" style="white-space:nowrap;">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ number_format($producto->distancia, 1) }} km
                            </span>
                        @endif
                    </div>

                    <p class="mb-1"><strong>Categoría:</strong> {{ $producto->categoria }}</p>
                    <p class="mb-1"><strong>Precio:</strong> Bs {{ number_format($producto->precio, 2) }}</p>
                    <p class="mb-1">
                        <strong>Disponible:</strong>
                        {{ $producto->cantidad_disponible }} {{ $producto->unidad_medida }}
                    </p>
                    <p class="text-muted small mb-2">{{ Str::limit($producto->descripcion, 100) }}</p>

                    @if ($producto->estado_disponibilidad === 'preventa')
                        <span class="badge badge-warning mb-2">Preventa</span>

                        @php
                            $dias = max(0, (int) ceil(now()->diffInDays($producto->fecha_disponibilidad, false)));
                        @endphp
                        <p class="mb-1 small">
                            <strong>Disponible en:</strong>
                            {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                            ({{ $producto->fecha_disponibilidad->format('d/m/Y') }})
                        </p>

                        @if (auth()->user()->isComprador())
                            <form action="{{ route('preventas.store', $producto) }}" method="POST" class="mt-2">
                                @csrf
                                <div class="form-group mb-2">
                                    <label class="small">Cantidad a reservar</label>
                                    <input type="number" step="0.01" min="0.01"
                                           max="{{ $producto->cantidad_disponible }}"
                                           name="cantidad" class="form-control form-control-sm" required>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block btn-sm">
                                    Reservar — anticipo 40%
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="badge badge-success mb-2">Disponible ahora</span>
                    @endif

                    <hr class="my-2">
                    <small class="text-muted">
                        <i class="fas fa-user-circle mr-1"></i>
                        {{ $producto->productor->name ?? 'Productor' }}
                    </small>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            @if($filtroActivo)
                <div class="alert alert-warning">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    No hay productos de productores dentro de <strong>{{ $radio }} km</strong> de tu ubicación.
                    <a href="{{ route('productos.marketplace') }}" class="alert-link ml-2">Ver todos los productos</a>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-seedling mr-2"></i>
                    Todavía no hay productos publicados en el marketplace.
                </div>
            @endif
        </div>
    @endforelse
</div>

{{ $productos->links() }}

@endsection

@push('scripts')
<script>
document.getElementById('btn-geolocate').addEventListener('click', function () {
    const btn = this;
    const estado = document.getElementById('ubicacion-estado');

    if (!navigator.geolocation) {
        estado.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Tu navegador no soporta geolocalización.</span>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Detectando...';
    estado.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Obteniendo ubicación...</span>';

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);

            document.getElementById('input-lat').value = lat;
            document.getElementById('input-lng').value = lng;

            estado.innerHTML = `<span class="text-success"><i class="fas fa-check-circle mr-1"></i> Ubicación detectada (${lat}, ${lng})</span>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-crosshairs mr-1"></i> Detectar mi ubicación';
        },
        function (err) {
            const msgs = {
                1: 'Permiso de ubicación denegado.',
                2: 'No se pudo obtener la posición.',
                3: 'Tiempo de espera agotado.',
            };
            estado.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle mr-1"></i> ${msgs[err.code] || 'Error desconocido.'}</span>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-crosshairs mr-1"></i> Detectar mi ubicación';
        },
        { timeout: 10000, maximumAge: 60000 }
    );
});

// Validar que tenga ubicación si seleccionó radio
document.getElementById('filtro-form').addEventListener('submit', function (e) {
    const radio = document.getElementById('radio').value;
    const lat   = document.getElementById('input-lat').value;

    if (radio && !lat) {
        e.preventDefault();
        alert('Primero detecta tu ubicación para usar el filtro de cercanía.');
    }
});
</script>
@endpush
