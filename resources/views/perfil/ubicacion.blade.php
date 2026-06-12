@extends('layouts.adminlte')
@section('title', 'Ubicación del Predio')
@section('page_title', 'Ubicación GPS de mi Predio')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 450px; border-radius: 8px; border: 2px solid #dee2e6; }
        #map.picking { cursor: crosshair; border-color: #28a745; }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Selecciona la ubicación de tu finca</h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Haz clic en el mapa para marcar la ubicación exacta de tu predio. Puedes usar el botón de geolocalización para usar tu posición actual.
                </p>
                <div id="map"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="card-title"><i class="fas fa-save mr-2"></i>Guardar ubicación</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('perfil.ubicacion.guardar') }}" method="POST" id="formUbicacion">
                    @csrf
                    <div class="form-group">
                        <label>Latitud</label>
                        <input type="text" id="latitud" name="latitud" class="form-control @error('latitud') is-invalid @enderror"
                               value="{{ old('latitud', $productor->latitud) }}" readonly placeholder="Haz clic en el mapa">
                        @error('latitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Longitud</label>
                        <input type="text" id="longitud" name="longitud" class="form-control @error('longitud') is-invalid @enderror"
                               value="{{ old('longitud', $productor->longitud) }}" readonly placeholder="Haz clic en el mapa">
                        @error('longitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Dirección detectada</label>
                        <textarea id="direccionDetectada" class="form-control" rows="2" readonly placeholder="Se detectará automáticamente..."></textarea>
                    </div>

                    <button type="button" class="btn btn-outline-info btn-block mb-2" onclick="usarMiUbicacion()">
                        <i class="fas fa-location-arrow mr-1"></i> Usar mi ubicación actual
                    </button>
                    <button type="submit" class="btn btn-success btn-block" id="btnGuardar" {{ !$productor->latitud ? 'disabled' : '' }}>
                        <i class="fas fa-save mr-1"></i> Guardar ubicación
                    </button>
                </form>

                @if($productor->latitud && $productor->longitud)
                    <hr>
                    <div class="alert alert-success py-2 mb-0">
                        <i class="fas fa-check-circle mr-1"></i> Ubicación registrada<br>
                        <small>{{ $productor->latitud }}, {{ $productor->longitud }}</small>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6><i class="fas fa-info-circle mr-1 text-info"></i> ¿Para qué sirve?</h6>
                <ul class="text-muted small mb-0">
                    <li>Los compradores podrán filtrar productos por cercanía.</li>
                    <li>Da trazabilidad y confianza en la compra.</li>
                    <li>Puedes actualizar esta ubicación en cualquier momento.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const latInicial  = {{ old('latitud', $productor->latitud) ?? -17.7833 }};
    const lngInicial  = {{ old('longitud', $productor->longitud) ?? -63.1821 }};
    const tieneUbicacion = {{ old('latitud', $productor->latitud) ? 'true' : 'false' }};

    const map = L.map('map').setView([latInicial, lngInicial], tieneUbicacion ? 14 : 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    if (tieneUbicacion) {
        marker = L.marker([latInicial, lngInicial]).addTo(map)
            .bindPopup('📍 Ubicación actual de tu predio').openPopup();
        obtenerDireccion(latInicial, lngInicial);
    }

    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(7);
        const lng = e.latlng.lng.toFixed(7);
        colocarMarcador(lat, lng);
    });

    function colocarMarcador(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map)
            .bindPopup('📍 Ubicación seleccionada').openPopup();
        document.getElementById('latitud').value  = lat;
        document.getElementById('longitud').value = lng;
        document.getElementById('btnGuardar').disabled = false;
        obtenerDireccion(lat, lng);
    }

    function obtenerDireccion(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('direccionDetectada').value = data.display_name || 'No se pudo obtener la dirección';
            })
            .catch(() => {
                document.getElementById('direccionDetectada').value = 'Error al obtener dirección';
            });
    }

    function usarMiUbicacion() {
        if (!navigator.geolocation) {
            alert('Tu navegador no soporta geolocalización.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude.toFixed(7);
                const lng = pos.coords.longitude.toFixed(7);
                map.setView([lat, lng], 14);
                colocarMarcador(lat, lng);
            },
            () => alert('No se pudo obtener tu ubicación. Verifica los permisos del navegador.')
        );
    }
</script>
@endpush
