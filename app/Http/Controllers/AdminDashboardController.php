<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SolicitudVendedor;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalUsuarios    = User::count();
        $totalProductores = User::whereHas('role', fn($q) => $q->where('nombre', 'productor'))->count();
        $totalCompradores = User::whereHas('role', fn($q) => $q->where('nombre', 'comprador'))->count();
        $totalTransportistas = User::whereHas('role', fn($q) => $q->where('nombre', 'transportista'))->count();

        $solicitudesPendientes = SolicitudVendedor::where('estado', 'pendiente')->count();
        $solicitudesAprobadas  = SolicitudVendedor::where('estado', 'aprobada')->count();
        $solicitudesRechazadas = SolicitudVendedor::where('estado', 'rechazada')->count();

        // Usuarios pendientes de verificación sin solicitud registrada
        $usuariosPendientesSinSolicitud = User::where('estado', 'pendiente_verificacion')
            ->whereDoesntHave('solicitudesVendedor')
            ->count();

        $ultimasSolicitudes = SolicitudVendedor::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'usuariosPendientesSinSolicitud',
            'totalUsuarios',
            'totalProductores',
            'totalCompradores',
            'totalTransportistas',
            'solicitudesPendientes',
            'solicitudesAprobadas',
            'solicitudesRechazadas',
            'ultimasSolicitudes'
        ));
    }
}
