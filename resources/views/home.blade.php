@extends('layouts.adminlte')
@section('title', 'Inicio')
@section('page_title', 'Bienvenido')

@section('content')
@php
    $user = auth()->user();
    $productor = $user->isProductor() ? $user->productor : null;
@endphp

@if($user->estaPendiente())
    <div class="alert alert-warning">
        <h5><i class="fas fa-clock mr-2"></i> Tu cuenta está pendiente de verificación</h5>
        <p class="mb-2">El administrador revisará tus documentos y te notificará cuando tu cuenta sea verificada. Mientras tanto, tienes acceso de solo lectura.</p>
        @if(!$user->solicitudPendiente())
            <a href="{{ route('verificacion.create') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-file-upload mr-1"></i> Subir documentos
            </a>
        @else
            <span class="badge badge-warning">Documentos enviados — en revisión</span>
        @endif
    </div>
@endif

<div class="row">
    @if($user->isProductor())
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-body text-center py-4">
                    <i class="fas fa-map-marker-alt fa-3x text-success mb-3"></i>
                    <h5>Ubicación de tu Predio</h5>
                    <p class="text-muted">
                        @if($productor && $productor->latitud && $productor->longitud)
                            <span class="text-success"><i class="fas fa-check-circle"></i> Ubicación registrada</span><br>
                            <small>{{ $productor->latitud }}, {{ $productor->longitud }}</small>
                        @else
                            Aún no has registrado la ubicación de tu finca.
                        @endif
                    </p>
                    <a href="{{ route('perfil.ubicacion') }}" class="btn btn-success">
                        <i class="fas fa-map mr-1"></i>
                        {{ $productor && $productor->latitud ? 'Actualizar ubicación' : 'Registrar ubicación' }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-body py-4">
                    <h5><i class="fas fa-id-card mr-2"></i> Mi perfil de Productor</h5>
                    <table class="table table-sm mb-0">
                        <tr><th>Nombre:</th><td>{{ $user->name }}</td></tr>
                        <tr><th>Finca:</th><td>{{ $productor->nombre_finca ?? '—' }}</td></tr>
                        <tr><th>Tipo:</th><td>{{ $productor->tipo_productor ?? '—' }}</td></tr>
                        <tr><th>Ubicación:</th><td>{{ $productor->ubicacion_administrativa ?? '—' }}</td></tr>
                        <tr><th>Experiencia:</th><td>{{ $productor && $productor->años_experiencia ? $productor->años_experiencia . ' años' : '—' }}</td></tr>
                        <tr><th>Estado:</th>
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
                    </table>
                </div>
            </div>
        </div>

    @elseif($user->isComprador())
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-body py-4">
                    <h5><i class="fas fa-id-card mr-2"></i> Mi perfil de Comprador</h5>
                    <table class="table table-sm mb-0">
                        <tr><th>Nombre:</th><td>{{ $user->name }}</td></tr>
                        <tr><th>Tipo:</th><td>{{ $user->tipo_comprador ?? '—' }}</td></tr>
                        <tr><th>Zona de compra:</th><td>{{ $user->zona_compra ?? '—' }}</td></tr>
                        <tr><th>Estado:</th><td><span class="badge badge-success">Activo</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

    @elseif($user->isTransportista())
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-body py-4">
                    <h5><i class="fas fa-truck mr-2"></i> Mi perfil de Transportista</h5>
                    <table class="table table-sm mb-0">
                        <tr><th>Nombre:</th><td>{{ $user->name }}</td></tr>
                        <tr><th>Tipo transporte:</th><td>{{ $user->tipo_transporte ?? '—' }}</td></tr>
                        <tr><th>Capacidad:</th><td>{{ $user->capacidad_carga ?? '—' }}</td></tr>
                        <tr><th>Zona:</th><td>{{ $user->zona_operacion ?? '—' }}</td></tr>
                        <tr><th>Placa:</th><td>{{ $user->placa_vehiculo ?? '—' }}</td></tr>
                        <tr><th>Estado:</th>
                            <td>
                                @if($user->estaVerificado())
                                    <span class="badge badge-success">Verificado</span>
                                @else
                                    <span class="badge badge-warning">Pendiente verificación</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    @elseif($user->isAdmin())
        <div class="col-12">
            <div class="callout callout-success">
                <h5>Panel de Administrador</h5>
                <p>Desde aquí puedes gestionar los usuarios y validar documentos.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-success mr-2">
                    <i class="fas fa-chart-bar mr-1"></i> Ver Dashboard
                </a>
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-warning">
                    <i class="fas fa-clipboard-list mr-1"></i> Validar Documentos
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
