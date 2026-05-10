@extends('layouts.adminlte')
@section('title', 'Subir Documentos')
@section('page_title', 'Verificación de cuenta — Subir documentos')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">

        @if(auth()->user()->solicitudPendiente())
            <div class="alert alert-info">
                <h5><i class="fas fa-clock mr-2"></i>Documentos en revisión</h5>
                <p class="mb-0">Ya enviaste tus documentos. El administrador los está revisando. Te notificaremos cuando tu cuenta sea verificada.</p>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title"><i class="fas fa-file-upload mr-2"></i>Subir documentos de verificación</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Para activar completamente tu cuenta como <strong>{{ ucfirst(auth()->user()->role_name) }}</strong>, el administrador debe verificar tu identidad. Sube los documentos requeridos.</p>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('verificacion.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Motivo / descripción <span class="text-danger">*</span></label>
                            <textarea name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                                      rows="3" placeholder="Describe brevemente tu actividad...">{{ old('motivo') }}</textarea>
                            @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Teléfono de contacto <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', auth()->user()->telefono) }}">
                            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                   value="{{ old('direccion') }}" placeholder="Tu dirección actual">
                            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Número de documento</label>
                            <input type="text" name="documento" class="form-control"
                                   value="{{ old('documento', auth()->user()->documento_identidad) }}">
                        </div>
                        <div class="form-group">
                            <label>Archivo del documento <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('archivo_documento') is-invalid @enderror"
                                       name="archivo_documento" id="archivoDoc" accept=".pdf,.jpg,.jpeg,.png">
                                <label class="custom-file-label" for="archivoDoc">Seleccionar archivo...</label>
                            </div>
                            <small class="text-muted">PDF, JPG o PNG — máx. 5MB</small>
                            @error('archivo_documento')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-paper-plane mr-1"></i> Enviar para revisión
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.getElementById('archivoDoc').addEventListener('change', function() {
    if (this.files.length) this.nextElementSibling.textContent = this.files[0].name;
});
</script>
@endsection
