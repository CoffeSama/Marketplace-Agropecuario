@extends('layouts.adminlte')

@section('title', $titulo)
@section('page_title', $titulo)

@push('styles')
<style>
    .qr-payment-shell { max-width: 980px; margin: 0 auto; }
    .qr-card { border: 0; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); }
    .fake-qr {
        width: 260px;
        height: 260px;
        display: grid;
        grid-template-columns: repeat(13, 1fr);
        grid-template-rows: repeat(13, 1fr);
        gap: 4px;
        padding: 14px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #d8e2dc;
        border-radius: 8px;
    }
    .fake-qr span { background: #f8fafc; border-radius: 2px; }
    .fake-qr span.is-dark { background: #111827; }
    .fake-qr span.is-anchor { background: #0f5132; }
    .qr-reference { font-family: monospace; word-break: break-word; }
</style>
@endpush

@section('content')
@php
    $seed = crc32($qrPayload);
    $estadoTexto = $preventa->estado === 'completado' ? 'Completado' : 'Pendiente de saldo';
@endphp

<div class="qr-payment-shell">
    <div class="row">
        <div class="col-lg-5 mb-3">
            <div class="card qr-card h-100">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-qrcode mr-1"></i> Escanea el QR
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="fake-qr" aria-label="QR simulado de preventa {{ $preventa->id }}">
                        @for ($i = 0; $i < 169; $i++)
                            @php
                                $row = intdiv($i, 13);
                                $col = $i % 13;
                                $isAnchor = ($row < 3 && $col < 3)
                                    || ($row < 3 && $col > 9)
                                    || ($row > 9 && $col < 3);
                                $isDark = $isAnchor || (($seed + ($i * 19) + ($row * $col)) % 5 < 2);
                            @endphp
                            <span class="{{ $isAnchor ? 'is-anchor' : ($isDark ? 'is-dark' : '') }}"></span>
                        @endfor
                    </div>

                    <h4 class="mt-4 mb-1">Bs {{ number_format($monto, 2) }}</h4>
                    <p class="text-muted mb-0">QR ficticio para demostración del MVP</p>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-3">
            <div class="card qr-card h-100">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Datos del pago</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-3">
                        <dt class="col-sm-4">Preventa</dt>
                        <dd class="col-sm-8">#{{ $preventa->id }}</dd>

                        <dt class="col-sm-4">Producto</dt>
                        <dd class="col-sm-8">{{ $preventa->producto->nombre }}</dd>

                        <dt class="col-sm-4">Productor</dt>
                        <dd class="col-sm-8">{{ $preventa->producto->productor->name ?? 'Sin productor asignado' }}</dd>

                        <dt class="col-sm-4">Tipo de pago</dt>
                        <dd class="col-sm-8">{{ ucfirst($tipo) }}</dd>

                        <dt class="col-sm-4">Monto a pagar</dt>
                        <dd class="col-sm-8">Bs {{ number_format($monto, 2) }}</dd>

                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8">{{ $estadoTexto }}</dd>
                    </dl>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $descripcion }} Al confirmarlo, el pago quedara registrado como simulado.
                    </div>

                    <p class="small text-muted mb-3">
                        Referencia simulada:
                        <span class="qr-reference">{{ $qrPayload }}</span>
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('mis-preventas') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>

                        <form action="{{ $rutaConfirmacion }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle mr-1"></i> Simular pago realizado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
