@extends('layouts.adminlte')

@section('title', 'Agregar producto')
@section('page_title', 'Agregar producto de cosecha')

@section('content')

<div class="card">
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Corrige los siguientes errores:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-3">
                <label>Nombre del producto *</label>
                <input type="text"
                       name="nombre"
                       class="form-control"
                       value="{{ old('nombre') }}"
                       placeholder="Ej. Papa holandesa, tomate, cebolla">
            </div>

            <div class="form-group mb-3">
                <label>Categoría *</label>
                <select name="categoria" class="form-control">
                    <option value="">Seleccione una categoría</option>
                    <option value="Verdura" {{ old('categoria') == 'Verdura' ? 'selected' : '' }}>Verdura</option>
                    <option value="Fruta" {{ old('categoria') == 'Fruta' ? 'selected' : '' }}>Fruta</option>
                    <option value="Tubérculo" {{ old('categoria') == 'Tubérculo' ? 'selected' : '' }}>Tubérculo</option>
                    <option value="Cereal" {{ old('categoria') == 'Cereal' ? 'selected' : '' }}>Cereal</option>
                    <option value="Legumbre" {{ old('categoria') == 'Legumbre' ? 'selected' : '' }}>Legumbre</option>
                    <option value="Otro" {{ old('categoria') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Precio *</label>
                <input type="number"
                       step="0.01"
                       name="precio"
                       class="form-control"
                       value="{{ old('precio') }}"
                       placeholder="Ej. 50">
            </div>

            <div class="form-group mb-3">
                <label>Cantidad disponible *</label>
                <input type="number"
                       step="0.01"
                       name="cantidad_disponible"
                       class="form-control"
                       value="{{ old('cantidad_disponible') }}"
                       placeholder="Ej. 10">
            </div>

            <div class="form-group mb-3">
                <label>Unidad de medida *</label>
                <select name="unidad_medida" class="form-control">
                    <option value="">Seleccione una unidad</option>
                    <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                    <option value="arroba" {{ old('unidad_medida') == 'arroba' ? 'selected' : '' }}>Arroba</option>
                    <option value="quintal" {{ old('unidad_medida') == 'quintal' ? 'selected' : '' }}>Quintal</option>
                    <option value="unidad" {{ old('unidad_medida') == 'unidad' ? 'selected' : '' }}>Unidad</option>
                    <option value="caja" {{ old('unidad_medida') == 'caja' ? 'selected' : '' }}>Caja</option>
                    <option value="saco" {{ old('unidad_medida') == 'saco' ? 'selected' : '' }}>Saco</option>
                    <option value="tonelada" {{ old('unidad_medida') == 'tonelada' ? 'selected' : '' }}>Tonelada</option>
                    <option value="libra" {{ old('unidad_medida') == 'libra' ? 'selected' : '' }}>Libra</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Descripción *</label>
                <textarea name="descripcion"
                          class="form-control"
                          rows="4"
                          placeholder="Describe el producto, calidad, origen, cosecha, etc.">{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-group mb-3">
                <label>Imágenes del producto *</label>
                <input type="file"
                       name="imagenes[]"
                       class="form-control"
                       accept="image/jpeg,image/png,image/webp"
                       multiple>

                <small class="text-muted">
                    Formatos permitidos: JPG, PNG, WEBP. Máximo 2MB por imagen.
                </small>
            </div>

            <button type="submit" class="btn btn-success">
                Guardar y publicar
            </button>

            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
    </div>
</div>

@endsection