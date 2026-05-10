@extends('layouts.adminlte')
@section('title', 'Validar Documentos')
@section('page_title', 'Validación de Documentos de Usuarios')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-clipboard-list mr-2"></i>Solicitudes de verificación</h3>
        <form method="GET" class="d-flex gap-2">
            <select name="estado" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendientes</option>
                <option value="aprobada"   {{ request('estado') === 'aprobada'   ? 'selected' : '' }}>Aprobadas</option>
                <option value="rechazada"  {{ request('estado') === 'rechazada'  ? 'selected' : '' }}>Rechazadas</option>
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Teléfono</th>
                    <th>Motivo</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $sol)
                    <tr>
                        <td>
                            <strong>{{ $sol->user->name ?? '—' }}</strong><br>
                            <small class="text-muted">{{ $sol->user->email ?? '—' }}</small>
                        </td>
                        <td><span class="badge badge-info">{{ ucfirst($sol->user->role_name ?? '—') }}</span></td>
                        <td>{{ $sol->telefono }}</td>
                        <td><small>{{ Str::limit($sol->motivo, 50) }}</small></td>
                        <td>
                            @if($sol->archivo_documento)
                                <a href="{{ asset('storage/'.$sol->archivo_documento) }}" target="_blank" class="btn btn-xs btn-info">
                                    <i class="fas fa-file"></i> Ver
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($sol->estado === 'pendiente')
                                <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Pendiente</span>
                            @elseif($sol->estado === 'aprobada')
                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Aprobada</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Rechazada</span>
                            @endif
                        </td>
                        <td><small>{{ $sol->created_at->format('d/m/Y') }}</small></td>
                        <td>
                            <a href="{{ route('admin.solicitudes.show', $sol) }}" class="btn btn-xs btn-primary mb-1">
                                <i class="fas fa-eye"></i> Detalle
                            </a>
                            @if($sol->estado === 'pendiente')
                                <form action="{{ route('admin.solicitudes.aprobar', $sol->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success mb-1"
                                            onclick="return confirm('¿Aprobar la solicitud de {{ $sol->user->name ?? 'este usuario' }}? Su estado cambiará a Verificado.')">
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                </form>
                                <form action="{{ route('admin.solicitudes.rechazar', $sol->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-danger mb-1"
                                            onclick="return confirm('¿Rechazar la solicitud de {{ $sol->user->name ?? 'este usuario' }}?')">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No hay solicitudes registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $solicitudes->links() }}
    </div>
</div>
@endsection
