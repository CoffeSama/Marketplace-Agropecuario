@extends('layouts.adminlte')
@section('title', 'Dashboard — Admin')
@section('page_title', 'Dashboard de Usuarios')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Usuarios</span>
                <span class="info-box-number">{{ $totalUsuarios }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-tractor"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Productores</span>
                <span class="info-box-number">{{ $totalProductores }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-shopping-basket"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Compradores</span>
                <span class="info-box-number">{{ $totalCompradores }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Transportistas</span>
                <span class="info-box-number">{{ $totalTransportistas }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pendientes de verificación</span>
                <span class="info-box-number">{{ $solicitudesPendientes }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Solicitudes aprobadas</span>
                <span class="info-box-number">{{ $solicitudesAprobadas }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Solicitudes rechazadas</span>
                <span class="info-box-number">{{ $solicitudesRechazadas }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Últimas solicitudes de verificación</h3>
        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-sm btn-outline-primary">
            Ver todas <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado solicitud</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasSolicitudes as $sol)
                    <tr>
                        <td>{{ $sol->user->name ?? '—' }}</td>
                        <td>{{ ucfirst($sol->user->role_name ?? '—') }}</td>
                        <td>
                            @if($sol->estado === 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @elseif($sol->estado === 'aprobada')
                                <span class="badge badge-success">Aprobada</span>
                            @else
                                <span class="badge badge-danger">Rechazada</span>
                            @endif
                        </td>
                        <td>{{ $sol->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.solicitudes.show', $sol) }}" class="btn btn-xs btn-info">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">No hay solicitudes aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
