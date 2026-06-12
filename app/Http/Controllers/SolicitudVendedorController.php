<?php

namespace App\Http\Controllers;

use App\Models\SolicitudVendedor;
use App\Models\User;
use App\Models\Role;
use App\Models\Productor;
use App\Http\Requests\StoreSolicitudVendedorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SolicitudVendedorController extends Controller
{
    /**
     * Mostrar formulario de solicitud (Cliente)
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para enviar una solicitud.');
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('home')
                ->with('info', 'Ya tienes permisos de administrador.');
        }

        // Verificar si el usuario ya tiene una solicitud pendiente
        $solicitudPendiente = $user->solicitudPendiente();
        if ($solicitudPendiente) {
            return view('solicitudes_vendedor.create')
                ->with('info', 'Ya tienes una solicitud pendiente. Espera la respuesta del administrador.');
        }

        return view('solicitudes_vendedor.create');
    }

    /**
     * Guardar solicitud (Cliente)
     */
    public function store(StoreSolicitudVendedorRequest $request)
    {
        $user = Auth::user();

        $solicitudPendiente = $user->solicitudPendiente();
        if ($solicitudPendiente) {
            return redirect()->route('verificacion.create')
                ->with('error', 'Ya tienes una solicitud pendiente. No puedes enviar otra hasta que sea procesada.')
                ->withInput();
        }

        $data = [
            'user_id' => $user->id,
            'motivo' => $request->validated()['motivo'],
            'telefono' => $request->validated()['telefono'],
            'direccion' => $request->validated()['direccion'],
            'documento' => $request->validated()['documento'] ?? null,
            'estado' => 'pendiente',
        ];

        // Guardar archivo si se subió
        if ($request->hasFile('archivo_documento')) {
            $data['archivo_documento'] = $request->file('archivo_documento')
                ->store('solicitudes_vendedor', 'public');
        }

        try {
            SolicitudVendedor::create($data);

            return redirect()->route('verificacion.create')
                ->with('success', 'Tu solicitud ha sido enviada correctamente. El administrador la revisará pronto.');
        } catch (\Exception $e) {
            return redirect()->route('verificacion.create')
                ->with('error', 'Hubo un error al guardar tu solicitud. Por favor, intenta nuevamente.')
                ->withInput();
        }
    }

    /**
     * Listar solicitudes (Admin)
     */
    public function index(Request $request)
    {
        $query = SolicitudVendedor::with('user');

        // Filtro por estado
        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        // Ordenar por fecha más reciente
        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('solicitudes_vendedor.index', compact('solicitudes'));
    }

    /**
     * Mostrar detalle de solicitud (Admin)
     */
    public function show(SolicitudVendedor $solicitudVendedor)
    {
        $solicitudVendedor->load('user');
        return view('solicitudes_vendedor.show', compact('solicitudVendedor'));
    }

    /**
     * Aprobar solicitud (Admin)
     */
    public function aprobar($id)
    {
        try {
            $solicitud = SolicitudVendedor::with('user')->findOrFail($id);

            // Validar que no esté ya aprobada o rechazada
            if ($solicitud->estado !== 'pendiente') {
                return redirect()->route('admin.solicitudes.index')
                    ->with('error', 'Esta solicitud ya ha sido procesada.');
            }

            $solicitud->estado = 'aprobada';
            $solicitud->fecha_revision_admin = now();
            $solicitud->save();

            $user = $solicitud->user;
            if ($user) {
                $user->estado = 'verificado';
                $user->save();
            }

            return redirect()->route('admin.solicitudes.index')
                ->with('success', "Usuario {$user->name} verificado correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('admin.solicitudes.index')
                ->with('error', 'Hubo un error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    public function ubicacion()
    {
        $user = Auth::user();

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden registrar la ubicación de su predio.');
        }

        $productor = $this->obtenerOCrearPerfilProductor($user);

        return view('perfil.ubicacion', compact('user', 'productor'));
    }

    public function guardarUbicacion(Request $request)
    {
        $request->validate([
            'latitud'  => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        $user = Auth::user();

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden registrar la ubicación de su predio.');
        }

        $productor = $this->obtenerOCrearPerfilProductor($user);

        $productor->update([
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ]);

        return redirect()->route('perfil.ubicacion')
            ->with('success', 'Ubicación de tu predio guardada correctamente.');
    }

    private function obtenerOCrearPerfilProductor(User $user): Productor
    {
        if ($user->productor) {
            return $user->productor;
        }

        return Productor::create([
            'user_id' => $user->id,
            'tipo_productor' => $user->getAttribute('tipo_productor') ?: 'Productor agropecuario',
            'nombre_finca' => $user->getAttribute('nombre_finca') ?: 'Finca de ' . $user->name,
            'ubicacion_administrativa' => $user->getAttribute('ubicacion_administrativa') ?: 'Por registrar',
            'años_experiencia' => $user->getAttribute('años_experiencia') ?: 0,
            'documento_identidad' => $user->getAttribute('documento_identidad') ?: 'SIN-DOC-' . $user->id,
            'archivo_documento' => $user->getAttribute('archivo_documento'),
        ]);
    }

    /**
     * Rechazar solicitud (Admin)
     */
    public function rechazar($id)
    {
        try {
            $solicitud = SolicitudVendedor::with('user')->findOrFail($id);

            // Validar que no esté ya aprobada o rechazada
            if ($solicitud->estado !== 'pendiente') {
                return redirect()->route('admin.solicitudes.index')
                    ->with('error', 'Esta solicitud ya ha sido procesada.');
            }

            // Cambiar estado
            $solicitud->estado = 'rechazada';
            $solicitud->fecha_revision_admin = now();
            $solicitud->save();

            $userName = $solicitud->user ? $solicitud->user->name : 'Usuario';
            return redirect()->route('admin.solicitudes.index')
                ->with('success', "Solicitud de {$userName} rechazada correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('admin.solicitudes.index')
                ->with('error', 'Hubo un error al rechazar la solicitud: ' . $e->getMessage());
        }
    }
}
