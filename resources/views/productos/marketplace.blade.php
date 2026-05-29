@extends('layouts.adminlte')

@section('title', 'Marketplace')
@section('page_title', 'Marketplace Agropecuario')

@section('content')

{{-- Panel de filtros --}}
<div class="card card-outline card-success mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtros de búsqueda</h3>
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

                {{-- Categoría --}}
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label><strong>Categoría</strong></label>
                        <select name="categoria" class="form-control">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}" {{ $categoria === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Ubicación --}}
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label><strong>Mi ubicación</strong></label>
                        <div id="ubicacion-estado" class="small mb-1">
                            @if(request('lat') && request('lng'))
                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Detectada</span>
                            @else
                                <span class="text-muted"><i class="fas fa-info-circle mr-1"></i> Sin ubicación</span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btn-geolocate">
                            <i class="fas fa-crosshairs mr-1"></i> Detectar ubicación
                        </button>
                    </div>
                </div>

                {{-- Radio --}}
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label><strong>Radio</strong></label>
                        <select name="radio" id="radio" class="form-control">
                            <option value="">Sin límite de distancia</option>
                            @foreach([10, 25, 50, 100] as $km)
                                <option value="{{ $km }}" {{ request('radio') == $km ? 'selected' : '' }}>
                                    {{ $km }} km
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-search mr-1"></i> Buscar
                    </button>
                    @if($filtroActivo || $categoria)
                        <a href="{{ route('productos.marketplace') }}"
                           class="btn btn-outline-secondary btn-block btn-sm mt-1">
                            <i class="fas fa-times mr-1"></i> Limpiar filtros
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($filtroActivo)
            <div class="alert alert-info mt-3 mb-0 py-2 small">
                <i class="fas fa-map-marker-alt mr-1"></i>
                Mostrando productos dentro de <strong>{{ $radio }} km</strong>, ordenados por cercanía.
            </div>
        @endif
    </div>
</div>

{{-- Grid de productos --}}
<div class="row">
    @forelse($productos as $producto)
        <div class="col-md-4 mb-3">
            <div class="card h-100">

                @if ($producto->imagenes->first())
                    <img src="{{ asset('storage/' . $producto->imagenes->first()->ruta) }}"
                         class="card-img-top" style="height:200px; object-fit:cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                         style="height:200px;">
                        <i class="fas fa-seedling fa-3x text-muted"></i>
                    </div>
                @endif

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h5 class="mb-0">{{ $producto->nombre }}</h5>
                        @if($filtroActivo && isset($producto->distancia))
                            <span class="badge badge-success ml-1" style="white-space:nowrap; font-size:0.8rem;">
                                <i class="fas fa-map-marker-alt"></i> {{ number_format($producto->distancia, 1) }} km
                            </span>
                        @endif
                    </div>

                    <span class="badge badge-secondary mb-2">{{ $producto->categoria }}</span>

                    <p class="mb-1"><strong>Precio:</strong> Bs {{ number_format($producto->precio, 2) }} / {{ $producto->unidad_medida }}</p>
                    <p class="mb-1"><strong>Stock:</strong> {{ $producto->cantidad_disponible }} {{ $producto->unidad_medida }}</p>
                    <p class="text-muted small mb-2">{{ Str::limit($producto->descripcion, 90) }}</p>

                    @if ($producto->estado_disponibilidad === 'preventa')
                        <span class="badge badge-warning mb-2">Preventa</span>
                        @php $dias = max(0, (int) ceil(now()->diffInDays($producto->fecha_disponibilidad, false))); @endphp
                        <p class="mb-1 small">
                            <i class="fas fa-calendar-alt mr-1 text-muted"></i>
                            Disponible en {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                            ({{ $producto->fecha_disponibilidad->format('d/m/Y') }})
                        </p>

                        @if (auth()->user()->isComprador())
                            <form action="{{ route('preventas.store', $producto) }}" method="POST" class="mt-2">
                                @csrf
                                <div class="input-group input-group-sm mb-2">
                                    <input type="number" step="0.01" min="0.01"
                                           max="{{ $producto->cantidad_disponible }}"
                                           name="cantidad" class="form-control" placeholder="Cantidad" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ $producto->unidad_medida }}</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block btn-sm">
                                    <i class="fas fa-shopping-cart mr-1"></i> Reservar — anticipo 40%
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="badge badge-success mb-2">Disponible ahora</span>
                    @endif

                </div>

                <div class="card-footer bg-white py-2">
                    <small class="text-muted d-block">
                        <i class="fas fa-user mr-1"></i> {{ $producto->productor->name ?? '—' }}
                    </small>
                    @php $ubicacion = $producto->productor->productor->ubicacion_administrativa ?? null; @endphp
                    @if($ubicacion)
                        <small class="text-muted d-block">
                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $ubicacion }}
                        </small>
                    @endif
                </div>

            </div>
        </div>
    @empty
        <div class="col-12">
            @if($filtroActivo || $categoria)
                <div class="alert alert-warning">
                    <i class="fas fa-search mr-2"></i>
                    No hay productos que coincidan con los filtros seleccionados.
                    <a href="{{ route('productos.marketplace') }}" class="alert-link ml-2">Ver todos</a>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-seedling mr-2"></i> Todavía no hay productos publicados en el marketplace.
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
        estado.innerHTML = '<span class="text-danger small">Navegador sin soporte de geolocalización.</span>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Detectando...';

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            document.getElementById('input-lat').value = pos.coords.latitude.toFixed(6);
            document.getElementById('input-lng').value = pos.coords.longitude.toFixed(6);
            estado.innerHTML = '<span class="text-success small"><i class="fas fa-check-circle mr-1"></i> Detectada</span>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-crosshairs mr-1"></i> Detectar ubicación';
        },
        function (err) {
            const msgs = { 1: 'Permiso denegado.', 2: 'No se pudo obtener posición.', 3: 'Tiempo agotado.' };
            estado.innerHTML = `<span class="text-danger small">${msgs[err.code] || 'Error.'}</span>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-crosshairs mr-1"></i> Detectar ubicación';
        },
        { timeout: 10000, maximumAge: 60000 }
    );
});

document.getElementById('filtro-form').addEventListener('submit', function (e) {
    const radio = document.getElementById('radio').value;
    const lat   = document.getElementById('input-lat').value;
    if (radio && !lat) {
        e.preventDefault();
        alert('Primero detecta tu ubicación para usar el filtro por radio.');
    }
});
</script>
@endpush
