@extends('layouts.adminlte')
@section('title', 'Detalle de Solicitud')
@section('page_title', 'Detalle de Solicitud de Verificación')

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user mr-2"></i>Datos del usuario</h3>
            </div>
            <div class="card-body">
                @php $user = $solicitudVendedor->user; @endphp
                <table class="table table-sm">
                    <tr><th>Nombre:</th><td>{{ $user->name }}</td></tr>
                    <tr><th>Email:</th><td>{{ $user->email }}</td></tr>
                    <tr><th>Rol:</th><td><span class="badge badge-info">{{ ucfirst($user->role_name) }}</span></td></tr>
                    <tr><th>Estado cuenta:</th>
                        <td>
                            @if($user->estaVerificado())
                                <span class="badge badge-success">Verificado</span>
                            @elseif($user->estaPendiente())
                                <span class="badge badge-warning">Pendiente verificación</span>
                            @else
                                <span class="badge badge-secondary">Registrado</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Teléfono:</th><td>{{ $solicitudVendedor->telefono }}</td></tr>
                    <tr><th>Dirección:</th><td>{{ $solicitudVendedor->direccion }}</td></tr>
                    <tr><th>Documento Nº:</th><td>{{ $solicitudVendedor->documento ?? '—' }}</td></tr>

                    @if($user->isProductor())
                        <tr><th colspan="2" class="bg-light">Datos de Productor</th></tr>
                        <tr><th>Tipo:</th><td>{{ $user->tipo_productor }}</td></tr>
                        <tr><th>Finca:</th><td>{{ $user->nombre_finca }}</td></tr>
                        <tr><th>Ubicación:</th><td>{{ $user->ubicacion_administrativa }}</td></tr>
                        <tr><th>Experiencia:</th><td>{{ $user->años_experiencia }} años</td></tr>
                        @if($user->latitud)
                            <tr><th>GPS finca:</th><td>{{ $user->latitud }}, {{ $user->longitud }}</td></tr>
                        @endif
                    @elseif($user->isTransportista())
                        <tr><th colspan="2" class="bg-light">Datos de Transportista</th></tr>
                        <tr><th>Tipo transporte:</th><td>{{ $user->tipo_transporte }}</td></tr>
                        <tr><th>Capacidad:</th><td>{{ $user->capacidad_carga }}</td></tr>
                        <tr><th>Zona:</th><td>{{ $user->zona_operacion }}</td></tr>
                        <tr><th>Licencia:</th><td>{{ $user->licencia_conducir }}</td></tr>
                        <tr><th>Placa:</th><td>{{ $user->placa_vehiculo }}</td></tr>
                    @endif

                    <tr><th>Motivo:</th><td>{{ $solicitudVendedor->motivo }}</td></tr>
                    <tr><th>Fecha solicitud:</th><td>{{ $solicitudVendedor->created_at->format('d/m/Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file mr-2"></i>Documento adjunto</h3>
            </div>
            <div class="card-body text-center">
                @if($solicitudVendedor->archivo_documento)
                    @php $ext = pathinfo($solicitudVendedor->archivo_documento, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                        <img src="{{ asset('storage/'.$solicitudVendedor->archivo_documento) }}"
                             class="img-fluid rounded mb-3" style="max-height:300px;">
                    @else
                        <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                        <p>Documento PDF</p>
                    @endif
                    <a href="{{ asset('storage/'.$solicitudVendedor->archivo_documento) }}"
                       target="_blank" class="btn btn-info btn-block">
                        <i class="fas fa-external-link-alt mr-1"></i> Ver documento completo
                    </a>
                @else
                    <p class="text-muted">No se adjuntó documento.</p>
                @endif
            </div>
        </div>

        @if($solicitudVendedor->estado === 'pendiente')
            <div class="card card-outline card-success">
                <div class="card-header"><h3 class="card-title">Decisión</h3></div>
                <div class="card-body">
                    <form action="{{ route('admin.solicitudes.aprobar', $solicitudVendedor->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block"
                                onclick="return confirm('¿Aprobar? El usuario quedará como Verificado.')">
                            <i class="fas fa-check-circle mr-1"></i> Aprobar — Verificar usuario
                        </button>
                    </form>
                    <form action="{{ route('admin.solicitudes.rechazar', $solicitudVendedor->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block"
                                onclick="return confirm('¿Rechazar la solicitud?')">
                            <i class="fas fa-times-circle mr-1"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert {{ $solicitudVendedor->estado === 'aprobada' ? 'alert-success' : 'alert-danger' }}">
                <h5>
                    <i class="fas fa-{{ $solicitudVendedor->estado === 'aprobada' ? 'check' : 'times' }}-circle mr-2"></i>
                    Solicitud {{ $solicitudVendedor->estado === 'aprobada' ? 'Aprobada' : 'Rechazada' }}
                </h5>
                @if($solicitudVendedor->fecha_revision_admin)
                    <small>Revisada el {{ $solicitudVendedor->fecha_revision_admin->format('d/m/Y H:i') }}</small>
                @endif
            </div>
        @endif

        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary btn-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a la lista
        </a>
    </div>
</div>
@endsection
