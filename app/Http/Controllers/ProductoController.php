<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden acceder a Mis productos.');
        }

        $productos = Producto::with('imagenes')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $user = Auth::user();

        /** @var \App\Models\User $user */

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden publicar productos.');
        }

        if (!$user->estaVerificado()) {
            return redirect()->route('productos.index')
                ->with('error', 'Tu cuenta debe estar verificada para publicar productos.');
        }

        return view('productos.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        /** @var \App\Models\User $user */

        if (!$user->isProductor()) {
            return redirect()->route('home')
                ->with('error', 'Solo los productores pueden publicar productos.');
        }

        if (!$user->estaVerificado()) {
            return redirect()->route('productos.index')
                ->with('error', 'Tu cuenta debe estar verificada para publicar productos.');
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'categoria' => ['required', 'string', 'max:80'],
            'precio' => ['required', 'numeric', 'min:0.01'],
            'cantidad_disponible' => ['required', 'numeric', 'min:0.01'],
            'unidad_medida' => ['required', 'in:kg,arroba,quintal,unidad,caja,saco,tonelada,libra'],
            'descripcion' => ['required', 'string', 'min:20', 'max:1000'],
            'imagenes' => ['required', 'array', 'min:1', 'max:5'],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'categoria.required' => 'La categoría es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.min' => 'El precio debe ser mayor a 0.',
            'cantidad_disponible.required' => 'La cantidad disponible es obligatoria.',
            'cantidad_disponible.min' => 'La cantidad debe ser mayor a 0.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'unidad_medida.in' => 'Selecciona una unidad de medida válida.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 20 caracteres.',
            'imagenes.required' => 'Debes subir al menos una imagen.',
            'imagenes.min' => 'Debes subir al menos una imagen.',
            'imagenes.*.image' => 'El archivo debe ser una imagen.',
            'imagenes.*.mimes' => 'Las imágenes deben ser JPG, JPEG, PNG o WEBP.',
            'imagenes.*.max' => 'Cada imagen no debe superar los 2MB.',
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            $producto = Producto::create([
                'user_id' => $user->id,
                'nombre' => $validated['nombre'],
                'categoria' => $validated['categoria'],
                'precio' => $validated['precio'],
                'cantidad_disponible' => $validated['cantidad_disponible'],
                'unidad_medida' => $validated['unidad_medida'],
                'descripcion' => $validated['descripcion'],
                'estado' => 'publicado',
            ]);

            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('productos', 'public');

                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta' => $ruta,
                ]);
            }
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto publicado correctamente en el marketplace.');
    }

    public function marketplace()
    {
        $productos = Producto::with(['imagenes', 'productor'])
            ->where('estado', 'publicado')
            ->latest()
            ->paginate(12);

        return view('productos.marketplace', compact('productos'));
    }
}
