<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\SolicitudVendedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('public.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $rol     = $request->rol;
        $roleObj = Role::where('nombre', $rol)->first();

        if (!$roleObj) {
            return back()->withInput()->withErrors(['rol' => 'Rol no válido.']);
        }

        $estado = in_array($rol, ['productor', 'transportista'])
            ? 'pendiente_verificacion'
            : 'registrado';

        $data = [
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'telefono'           => $request->telefono,
            'role_id'            => $roleObj->id,
            'estado'             => $estado,
            'terminos_aceptados' => true,
        ];

        if ($rol === 'productor') {
            $data += [
                'tipo_productor'          => $request->tipo_productor,
                'nombre_finca'            => $request->nombre_finca,
                'ubicacion_administrativa' => $request->ubicacion_administrativa,
                'años_experiencia'        => $request->años_experiencia,
                'documento_identidad'     => $request->documento_identidad,
            ];
            if ($request->hasFile('archivo_documento')) {
                $data['archivo_documento'] = $request->file('archivo_documento')
                    ->store('documentos', 'public');
            }
        }

        if ($rol === 'comprador') {
            $data += [
                'tipo_comprador' => $request->tipo_comprador,
                'zona_compra'    => $request->zona_compra,
            ];
        }

        if ($rol === 'transportista') {
            $data += [
                'tipo_transporte'  => $request->tipo_transporte,
                'capacidad_carga'  => $request->capacidad_carga,
                'zona_operacion'   => $request->zona_operacion,
                'licencia_conducir' => $request->licencia_conducir,
                'placa_vehiculo'   => $request->placa_vehiculo,
            ];
            if ($request->hasFile('archivo_documento')) {
                $data['archivo_documento'] = $request->file('archivo_documento')
                    ->store('documentos', 'public');
            }
        }

        $user = User::create($data);

        // Crear solicitud automática para roles que requieren verificación
        if (in_array($rol, ['productor', 'transportista'])) {
            $motivo = $rol === 'productor'
                ? "Productor registrado: {$request->tipo_productor} — Finca: {$request->nombre_finca}"
                : "Transportista registrado: {$request->tipo_transporte} — Placa: {$request->placa_vehiculo}";

            SolicitudVendedor::create([
                'user_id'           => $user->id,
                'motivo'            => $motivo,
                'telefono'          => $request->telefono,
                'direccion'         => $request->ubicacion_administrativa ?? $request->zona_operacion ?? '—',
                'documento'         => $request->documento_identidad ?? $request->licencia_conducir ?? null,
                'archivo_documento' => $user->archivo_documento,
                'estado'            => 'pendiente',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $msg = $estado === 'pendiente_verificacion'
            ? '¡Cuenta creada! Tu perfil está pendiente de verificación por el administrador.'
            : '¡Bienvenido al Marketplace Agropecuario!';

        return redirect()->route('home')->with('success', $msg);
    }
}
