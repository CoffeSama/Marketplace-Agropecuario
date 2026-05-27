<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Marketplace Agropecuario')</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-user-circle"></i>
                            <span class="ml-1">{{ auth()->user()->name }}</span>
                            <span class="badge badge-info ml-1">{{ ucfirst(auth()->user()->role_name) }}</span>
                            @if (auth()->user()->estaPendiente())
                                <span class="badge badge-warning ml-1">Pendiente verificación</span>
                            @elseif(auth()->user()->estaVerificado())
                                <span class="badge badge-success ml-1">Verificado</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">{{ auth()->user()->email }}</span>
                            <div class="dropdown-divider"></div>
                            @if (auth()->user()->isProductor())
                                <a href="{{ route('perfil.ubicacion') }}" class="dropdown-item">
                                    <i class="fas fa-map-marker-alt mr-2"></i> Ubicación de mi Predio
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('home') }}" class="brand-link">
                <span class="brand-text font-weight-light">🌾 Marketplace Agro</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column">
                        @auth
                            <li class="nav-item">
                                <a href="{{ route('home') }}"
                                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>Inicio</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('productos.marketplace') }}"
                                    class="nav-link {{ request()->routeIs('productos.marketplace') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-store"></i>
                                    <p>Marketplace</p>
                                </a>
                            </li>

                            @if (auth()->user()->isComprador())
                                <li class="nav-item">
                                    <a href="{{ route('mis-preventas') }}"
                                        class="nav-link {{ request()->routeIs('mis-preventas') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-shopping-cart"></i>
                                        <p>Mis preventas</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->user()->isProductor())
                                <li class="nav-header">MI PREDIDO</li>
                                <li class="nav-item">
                                    <a href="{{ route('productos.index') }}"
                                        class="nav-link {{ request()->routeIs('productos.*') && !request()->routeIs('productos.marketplace') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-seedling"></i>
                                        <p>Mis productos</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('ventas-futuras') }}"
                                        class="nav-link {{ request()->routeIs('ventas-futuras') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-calendar-alt"></i>
                                        <p>Ventas futuras</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('perfil.ubicacion') }}"
                                        class="nav-link {{ request()->routeIs('perfil.ubicacion*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-map-marker-alt"></i>
                                        <p>Ubicación GPS</p>
                                    </a>
                                </li>
                                @if (!auth()->user()->estaVerificado())
                                    <li class="nav-item">
                                        <a href="{{ route('verificacion.create') }}"
                                            class="nav-link {{ request()->routeIs('verificacion*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-upload"></i>
                                            <p>Subir Documentos</p>
                                        </a>
                                    </li>
                                @endif
                            @endif

                            @if (auth()->user()->isTransportista() && !auth()->user()->estaVerificado())
                                <li class="nav-header">VERIFICACIÓN</li>
                                <li class="nav-item">
                                    <a href="{{ route('verificacion.create') }}"
                                        class="nav-link {{ request()->routeIs('verificacion*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-upload"></i>
                                        <p>Subir Documentos</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->user()->isAdmin())
                                <li class="nav-header">ADMINISTRACIÓN</li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.solicitudes.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.solicitudes*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Validar Documentos</p>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item mt-3">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="nav-link text-left"
                                        style="background:none;border:none;color:#cfe6d0;">
                                        <i class="nav-icon fas fa-sign-out-alt"></i>
                                        <p>Cerrar Sesión</p>
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <h1>@yield('page_title', 'Panel')</h1>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('info') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer text-center text-sm">
            © {{ date('Y') }} Marketplace Agropecuario — Sprint 1
        </footer>
    </div>

    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
