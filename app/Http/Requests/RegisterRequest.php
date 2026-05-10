<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rol   = $this->input('rol');
        $rules = [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => ['required', 'string', 'confirmed', Password::min(8)],
            'telefono'  => 'required|string|max:20',
            'rol'       => 'required|in:productor,comprador,transportista',
            'terminos'  => 'accepted',
        ];

        if ($rol === 'productor') {
            $rules += [
                'tipo_productor'           => 'required|string|max:100',
                'nombre_finca'             => 'required|string|max:255',
                'ubicacion_administrativa' => 'required|string|max:255',
                'años_experiencia'         => 'required|integer|min:0|max:80',
                'documento_identidad'      => 'required|string|max:50',
                'archivo_documento'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ];
        }

        if ($rol === 'comprador') {
            $rules += [
                'tipo_comprador' => 'required|string|max:100',
                'zona_compra'    => 'required|string|max:255',
            ];
        }

        if ($rol === 'transportista') {
            $rules += [
                'tipo_transporte'   => 'required|string|max:100',
                'capacidad_carga'   => 'required|string|max:100',
                'zona_operacion'    => 'required|string|max:255',
                'licencia_conducir' => 'required|string|max:50',
                'placa_vehiculo'    => 'required|string|max:20',
                'archivo_documento' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'                    => 'El nombre completo es obligatorio.',
            'email.required'                   => 'El correo electrónico es obligatorio.',
            'email.unique'                     => 'Este correo ya está registrado.',
            'password.required'                => 'La contraseña es obligatoria.',
            'password.confirmed'               => 'Las contraseñas no coinciden.',
            'telefono.required'                => 'El número de celular es obligatorio.',
            'rol.required'                     => 'Debes seleccionar un rol.',
            'rol.in'                           => 'El rol seleccionado no es válido.',
            'terminos.accepted'                => 'Debes aceptar los términos y políticas.',
            'tipo_productor.required'          => 'El tipo de productor es obligatorio.',
            'nombre_finca.required'            => 'El nombre de la finca es obligatorio.',
            'ubicacion_administrativa.required' => 'La ubicación administrativa es obligatoria.',
            'años_experiencia.required'        => 'Los años de experiencia son obligatorios.',
            'documento_identidad.required'     => 'El documento de identidad es obligatorio.',
            'archivo_documento.required'       => 'Debes subir el documento de identidad.',
            'archivo_documento.mimes'          => 'El documento debe ser PDF, JPG o PNG.',
            'archivo_documento.max'            => 'El documento no puede superar 5MB.',
            'tipo_comprador.required'          => 'El tipo de comprador es obligatorio.',
            'zona_compra.required'             => 'La zona de compra es obligatoria.',
            'tipo_transporte.required'         => 'El tipo de transporte es obligatorio.',
            'capacidad_carga.required'         => 'La capacidad de carga es obligatoria.',
            'zona_operacion.required'          => 'La zona de operación es obligatoria.',
            'licencia_conducir.required'       => 'La licencia de conducir es obligatoria.',
            'placa_vehiculo.required'          => 'La placa del vehículo es obligatoria.',
        ];
    }
}
