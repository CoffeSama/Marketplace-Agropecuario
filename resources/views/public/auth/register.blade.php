<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — Marketplace Agropecuario</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        body { background: #f4f6f9; }
        .register-box { width: 620px; max-width: 96%; margin: 30px auto; }
        .role-card {
            cursor: pointer;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 18px 10px;
            text-align: center;
            transition: all .2s;
            user-select: none;
        }
        .role-card:hover { border-color: #28a745; background: #f6fff8; }
        .role-card.selected { border-color: #28a745; background: #eaffea; box-shadow: 0 0 0 3px rgba(40,167,69,.2); }
        .role-card i { font-size: 2rem; color: #28a745; }
        .role-section { display: none; }
        .role-section.active { display: block; }
        .section-divider { border-top: 2px solid #e9ecef; margin: 24px 0 18px; }
        .section-title { font-size: 1rem; font-weight: 600; color: #495057; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="register-box">
    <div class="text-center mb-3">
        <h2 class="font-weight-bold">🌾 Marketplace Agropecuario</h2>
        <p class="text-muted mb-0">Crea tu cuenta</p>
    </div>

    <div class="card card-outline card-success">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle mr-1"></i> Corrige los siguientes errores:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data" id="registerForm" novalidate>
                @csrf

                {{-- SELECCIÓN DE ROL --}}
                <p class="section-title"><i class="fas fa-user-tag mr-1 text-success"></i> ¿Cuál es tu rol?</p>
                <div class="row mb-3">
                    <div class="col-4">
                        <div class="role-card {{ old('rol') === 'productor' ? 'selected' : '' }}"
                             onclick="selectRol('productor', this)">
                            <i class="fas fa-tractor"></i>
                            <h6 class="mt-2 mb-0">Productor</h6>
                            <small class="text-muted">Vendo mis productos</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="role-card {{ old('rol') === 'comprador' ? 'selected' : '' }}"
                             onclick="selectRol('comprador', this)">
                            <i class="fas fa-shopping-basket"></i>
                            <h6 class="mt-2 mb-0">Comprador</h6>
                            <small class="text-muted">Compro productos</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="role-card {{ old('rol') === 'transportista' ? 'selected' : '' }}"
                             onclick="selectRol('transportista', this)">
                            <i class="fas fa-truck"></i>
                            <h6 class="mt-2 mb-0">Transportista</h6>
                            <small class="text-muted">Ofrezco transporte</small>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="rol" id="rolInput" value="{{ old('rol') }}">
                @error('rol')
                    <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                @enderror

                {{-- DATOS COMUNES --}}
                <div class="section-divider"></div>
                <p class="section-title"><i class="fas fa-id-card mr-1 text-success"></i> Datos personales</p>

                <div class="form-group">
                    <label>Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Tu nombre completo">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Número de celular <span class="text-danger">*</span></label>
                    <input type="text" name="telefono"
                           class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono') }}" placeholder="+591 70000000">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repite la contraseña">
                        </div>
                    </div>
                </div>

                {{-- CAMPOS POR ROL --}}
                <div id="role-fields" style="{{ old('rol') ? '' : 'display:none;' }}">

                    <div class="section-divider"></div>

                    {{-- PRODUCTOR --}}
                    <div class="role-section {{ old('rol') === 'productor' ? 'active' : '' }}" id="fields-productor">
                        <p class="section-title"><i class="fas fa-tractor mr-1 text-success"></i> Datos del Productor</p>

                        <div class="form-group">
                            <label>Tipo de productor <span class="text-danger">*</span></label>
                            <select name="tipo_productor"
                                    class="form-control @error('tipo_productor') is-invalid @enderror">
                                <option value="">Selecciona...</option>
                                @foreach(['Ganadero','Agricultor','Avicultor','Apicultor','Horticultor','Otro'] as $t)
                                    <option value="{{ $t }}" {{ old('tipo_productor') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('tipo_productor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Nombre de la finca <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_finca"
                                   class="form-control @error('nombre_finca') is-invalid @enderror"
                                   value="{{ old('nombre_finca') }}" placeholder="Ej: Finca El Paraíso">
                            @error('nombre_finca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Ubicación administrativa <span class="text-danger">*</span></label>
                            <input type="text" name="ubicacion_administrativa"
                                   class="form-control @error('ubicacion_administrativa') is-invalid @enderror"
                                   value="{{ old('ubicacion_administrativa') }}" placeholder="Ej: Santa Cruz, Bolivia">
                            @error('ubicacion_administrativa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Años de experiencia <span class="text-danger">*</span></label>
                                    <input type="number" name="años_experiencia"
                                           class="form-control @error('años_experiencia') is-invalid @enderror"
                                           value="{{ old('años_experiencia') }}" min="0" max="80" placeholder="0">
                                    @error('años_experiencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nº documento de identidad <span class="text-danger">*</span></label>
                                    <input type="text" name="documento_identidad"
                                           class="form-control @error('documento_identidad') is-invalid @enderror"
                                           value="{{ old('documento_identidad') }}" placeholder="CI / Pasaporte">
                                    @error('documento_identidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Documento de identidad (foto/PDF) <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('archivo_documento') is-invalid @enderror"
                                       name="archivo_documento" id="archivoProductor"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <label class="custom-file-label" for="archivoProductor">
                                    Seleccionar archivo...
                                </label>
                            </div>
                            <small class="text-muted">Acepta: PDF, JPG, JPEG, PNG — máx. 5MB</small>
                            @error('archivo_documento')
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning py-2 mb-0">
                            <i class="fas fa-clock mr-1"></i>
                            Tu cuenta quedará en <strong>Pendiente de verificación</strong> hasta que el administrador valide tus documentos.
                        </div>
                    </div>

                    {{-- COMPRADOR --}}
                    <div class="role-section {{ old('rol') === 'comprador' ? 'active' : '' }}" id="fields-comprador">
                        <p class="section-title"><i class="fas fa-shopping-basket mr-1 text-primary"></i> Datos del Comprador</p>

                        <div class="form-group">
                            <label>Tipo de comprador <span class="text-danger">*</span></label>
                            <select name="tipo_comprador"
                                    class="form-control @error('tipo_comprador') is-invalid @enderror">
                                <option value="">Selecciona...</option>
                                @foreach(['Persona natural','Empresa','Restaurante','Supermercado','Exportador','Otro'] as $t)
                                    <option value="{{ $t }}" {{ old('tipo_comprador') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('tipo_comprador')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Ciudad o zona principal de compra <span class="text-danger">*</span></label>
                            <input type="text" name="zona_compra"
                                   class="form-control @error('zona_compra') is-invalid @enderror"
                                   value="{{ old('zona_compra') }}" placeholder="Ej: Santa Cruz de la Sierra">
                            @error('zona_compra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-success py-2 mb-0">
                            <i class="fas fa-check-circle mr-1"></i>
                            Tu cuenta quedará <strong>activa inmediatamente</strong> al completar el registro.
                        </div>
                    </div>

                    {{-- TRANSPORTISTA --}}
                    <div class="role-section {{ old('rol') === 'transportista' ? 'active' : '' }}" id="fields-transportista">
                        <p class="section-title"><i class="fas fa-truck mr-1 text-warning"></i> Datos del Transportista</p>

                        <div class="form-group">
                            <label>Tipo de transporte <span class="text-danger">*</span></label>
                            <select name="tipo_transporte"
                                    class="form-control @error('tipo_transporte') is-invalid @enderror">
                                <option value="">Selecciona...</option>
                                @foreach(['Camión refrigerado','Camión ganadero','Camioneta','Furgón','Otro'] as $t)
                                    <option value="{{ $t }}" {{ old('tipo_transporte') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('tipo_transporte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Capacidad de carga <span class="text-danger">*</span></label>
                                    <input type="text" name="capacidad_carga"
                                           class="form-control @error('capacidad_carga') is-invalid @enderror"
                                           value="{{ old('capacidad_carga') }}" placeholder="Ej: 5 toneladas">
                                    @error('capacidad_carga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Placa del vehículo <span class="text-danger">*</span></label>
                                    <input type="text" name="placa_vehiculo"
                                           class="form-control @error('placa_vehiculo') is-invalid @enderror"
                                           value="{{ old('placa_vehiculo') }}" placeholder="Ej: 2345-SCZ">
                                    @error('placa_vehiculo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Zona de operación <span class="text-danger">*</span></label>
                            <input type="text" name="zona_operacion"
                                   class="form-control @error('zona_operacion') is-invalid @enderror"
                                   value="{{ old('zona_operacion') }}" placeholder="Ej: Oriente boliviano">
                            @error('zona_operacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Número de licencia de conducir <span class="text-danger">*</span></label>
                            <input type="text" name="licencia_conducir"
                                   class="form-control @error('licencia_conducir') is-invalid @enderror"
                                   value="{{ old('licencia_conducir') }}">
                            @error('licencia_conducir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Licencia de conducir (foto/PDF) <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('archivo_documento') is-invalid @enderror"
                                       name="archivo_documento" id="archivoTransportista"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <label class="custom-file-label" for="archivoTransportista">
                                    Seleccionar archivo...
                                </label>
                            </div>
                            <small class="text-muted">Acepta: PDF, JPG, JPEG, PNG — máx. 5MB</small>
                            @error('archivo_documento')
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning py-2 mb-0">
                            <i class="fas fa-clock mr-1"></i>
                            Tu cuenta quedará en <strong>Pendiente de verificación</strong> hasta que el administrador valide tus documentos.
                        </div>
                    </div>

                </div>{{-- /role-fields --}}

                {{-- TÉRMINOS --}}
                <div class="section-divider"></div>
                <div class="form-group mb-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input @error('terminos') is-invalid @enderror"
                               id="terminos" name="terminos" value="1" {{ old('terminos') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="terminos">
                            Acepto los <a href="#">términos y condiciones</a> y las <a href="#">políticas de privacidad</a>
                            <span class="text-danger">*</span>
                        </label>
                    </div>
                    @error('terminos')
                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success btn-block btn-lg">
                    <i class="fas fa-user-plus mr-1"></i> Crear cuenta
                </button>

            </form>

            <hr>
            <p class="text-center mb-0">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
            </p>

        </div>
    </div>
</div>

<script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    function selectRol(rol, el) {
        document.getElementById('rolInput').value = rol;
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');

        // Mostrar sección de campos del rol
        document.getElementById('role-fields').style.display = 'block';
        document.querySelectorAll('.role-section').forEach(s => s.classList.remove('active'));
        const sect = document.getElementById('fields-' + rol);
        if (sect) sect.classList.add('active');
    }

    // Actualizar etiqueta del input file
    document.querySelectorAll('.custom-file-input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.files.length) this.nextElementSibling.textContent = this.files[0].name;
        });
    });

    // Validación cliente antes de enviar
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const rol      = document.getElementById('rolInput').value;
        const password = document.querySelector('input[name="password"]').value;
        const confirm  = document.querySelector('input[name="password_confirmation"]').value;
        const errors   = [];

        if (!rol)                     errors.push('Selecciona un rol.');
        if (!password)                errors.push('La contraseña es obligatoria.');
        else if (password.length < 8) errors.push('La contraseña debe tener al menos 8 caracteres.');
        else if (password !== confirm) errors.push('Las contraseñas no coinciden.');

        if (errors.length > 0) {
            e.preventDefault();
            let div = document.getElementById('client-errors');
            if (!div) {
                div = document.createElement('div');
                div.id = 'client-errors';
                div.className = 'alert alert-danger';
                document.getElementById('registerForm').prepend(div);
            }
            div.innerHTML = '<strong>Corrige estos errores:</strong><ul class="mb-0 mt-1">'
                + errors.map(err => '<li>' + err + '</li>').join('') + '</ul>';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        // Quitar el atributo name de inputs en secciones inactivas
        // para que no se envíen con el form (más confiable que disabled)
        document.querySelectorAll('.role-section').forEach(function(section) {
            if (!section.classList.contains('active')) {
                section.querySelectorAll('input, select, textarea').forEach(function(el) {
                    el.removeAttribute('name');
                });
            }
        });
    });
</script>
</body>
</html>
