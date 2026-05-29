@extends('layouts.adminlte')

@section('title', 'Editar producto')
@section('page_title', 'Editar producto')

@section('content')

<div class="card">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrige los siguientes errores:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label>Nombre del producto *</label>
                <input type="text" name="nombre" class="form-control"
                       value="{{ old('nombre', $producto->nombre) }}">
            </div>

            <div class="form-group mb-3">
                <label>Categoría *</label>
                <select name="categoria" class="form-control">
                    <option value="">Seleccione una categoría</option>
                    @foreach(['Verdura','Fruta','Tubérculo','Cereal','Legumbre','Granos','Otro'] as $cat)
                        <option value="{{ $cat }}"
                            {{ old('categoria', $producto->categoria) === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Precio (Bs) *</label>
                        <input type="number" step="0.01" name="precio" class="form-control"
                               value="{{ old('precio', $producto->precio) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Cantidad disponible *</label>
                        <input type="number" step="0.01" name="cantidad_disponible" class="form-control"
                               value="{{ old('cantidad_disponible', $producto->cantidad_disponible) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Unidad de medida *</label>
                        <select name="unidad_medida" class="form-control">
                            @foreach(['kg'=>'Kilogramo','arroba'=>'Arroba','quintal'=>'Quintal','unidad'=>'Unidad','caja'=>'Caja','saco'=>'Saco','tonelada'=>'Tonelada','libra'=>'Libra'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('unidad_medida', $producto->unidad_medida) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label>Descripción *</label>
                <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div class="form-group mb-3">
                <label>Fecha de disponibilidad *</label>
                <input type="date" name="fecha_disponibilidad" class="form-control"
                       value="{{ old('fecha_disponibilidad', $producto->fecha_disponibilidad->format('Y-m-d')) }}">
                <small class="text-muted">Si la fecha es futura, el producto se marcará como preventa.</small>
            </div>

            <div class="form-group mb-3">
                <label>Imágenes actuales</label>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @foreach($producto->imagenes as $img)
                        <img src="{{ asset('storage/' . $img->ruta) }}"
                             style="height:80px; width:80px; object-fit:cover; border-radius:4px; border:1px solid #dee2e6;">
                    @endforeach
                </div>
                <label>Reemplazar imágenes <small class="text-muted">(opcional — sube nuevas para reemplazar todas)</small></label>
                <input type="file" name="imagenes[]" class="form-control"
                       accept="image/jpeg,image/png,image/webp" multiple>
                <small class="text-muted">Máximo 5 imágenes, 2MB c/u.</small>
            </div>

            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>

    </div>
</div>

@endsection
