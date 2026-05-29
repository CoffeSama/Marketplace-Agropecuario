<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

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
            'fecha_disponibilidad' => ['required', 'date', 'after_or_equal:today'],
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
            'fecha_disponibilidad.required' => 'La fecha de disponibilidad es obligatoria.',
            'fecha_disponibilidad.after_or_equal' => 'La fecha de disponibilidad no puede ser anterior a hoy.',
            'imagenes.required' => 'Debes subir al menos una imagen.',
            'imagenes.min' => 'Debes subir al menos una imagen.',
            'imagenes.*.image' => 'El archivo debe ser una imagen.',
            'imagenes.*.mimes' => 'Las imágenes deben ser JPG, JPEG, PNG o WEBP.',
            'imagenes.*.max' => 'Cada imagen no debe superar los 2MB.',
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            $estadoDisponibilidad = Carbon::parse($validated['fecha_disponibilidad'])->isFuture()
                ? 'preventa'
                : 'disponible';

            $producto = Producto::create([
                'user_id' => $user->id,
                'nombre' => $validated['nombre'],
                'categoria' => $validated['categoria'],
                'precio' => $validated['precio'],
                'cantidad_disponible' => $validated['cantidad_disponible'],
                'unidad_medida' => $validated['unidad_medida'],
                'descripcion' => $validated['descripcion'],
                'fecha_disponibilidad' => $validated['fecha_disponibilidad'],
                'estado_disponibilidad' => $estadoDisponibilidad,
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

    public function edit(Producto $producto)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($producto->user_id !== $user->id) {
            return redirect()->route('productos.index')
                ->with('error', 'No tienes permiso para editar este producto.');
        }

        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($producto->user_id !== $user->id) {
            return redirect()->route('productos.index')
                ->with('error', 'No tienes permiso para editar este producto.');
        }

        $validated = $request->validate([
            'nombre'              => ['required', 'string', 'max:120'],
            'categoria'           => ['required', 'string', 'max:80'],
            'precio'              => ['required', 'numeric', 'min:0.01'],
            'cantidad_disponible' => ['required', 'numeric', 'min:0.01'],
            'unidad_medida'       => ['required', 'in:kg,arroba,quintal,unidad,caja,saco,tonelada,libra'],
            'descripcion'         => ['required', 'string', 'min:20', 'max:1000'],
            'fecha_disponibilidad'=> ['required', 'date'],
            'imagenes'            => ['nullable', 'array', 'max:5'],
            'imagenes.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $validated, $producto) {
            $estadoDisponibilidad = Carbon::parse($validated['fecha_disponibilidad'])->isFuture()
                ? 'preventa'
                : 'disponible';

            $producto->update([
                'nombre'               => $validated['nombre'],
                'categoria'            => $validated['categoria'],
                'precio'               => $validated['precio'],
                'cantidad_disponible'  => $validated['cantidad_disponible'],
                'unidad_medida'        => $validated['unidad_medida'],
                'descripcion'          => $validated['descripcion'],
                'fecha_disponibilidad' => $validated['fecha_disponibilidad'],
                'estado_disponibilidad'=> $estadoDisponibilidad,
            ]);

            if ($request->hasFile('imagenes')) {
                foreach ($producto->imagenes as $img) {
                    Storage::disk('public')->delete($img->ruta);
                    $img->delete();
                }
                foreach ($request->file('imagenes') as $imagen) {
                    ProductoImagen::create([
                        'producto_id' => $producto->id,
                        'ruta'        => $imagen->store('productos', 'public'),
                    ]);
                }
            }
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function marketplace(Request $request)
    {
        $lat       = $request->filled('lat')       ? (float)  $request->lat       : null;
        $lng       = $request->filled('lng')       ? (float)  $request->lng       : null;
        $radio     = $request->filled('radio')     ? (int)    $request->radio     : null;
        $categoria = $request->filled('categoria') ? $request->categoria          : null;

        $filtroActivo = $lat !== null && $lng !== null && $radio !== null;

        $haversine = "6371 * acos(LEAST(1.0,
            cos(radians(?)) * cos(radians(productores.latitud)) * cos(radians(productores.longitud) - radians(?))
            + sin(radians(?)) * sin(radians(productores.latitud))
        ))";

        $categorias = ['Verdura', 'Fruta', 'Tubérculo', 'Cereal', 'Legumbre', 'Granos', 'Otro'];

        $query = Producto::with(['imagenes', 'productor', 'productor.productor'])
            ->where('productos.estado', 'publicado');

        if ($categoria) {
            $query->where('productos.categoria', $categoria);
        }

        if ($filtroActivo) {
            $query->join('productores', 'productores.user_id', '=', 'productos.user_id')
                  ->whereNotNull('productores.latitud')
                  ->whereNotNull('productores.longitud')
                  ->selectRaw("productos.*, $haversine as distancia", [$lat, $lng, $lat])
                  ->whereRaw("$haversine <= ?", [$lat, $lng, $lat, $radio])
                  ->orderBy('distancia');
        } else {
            $query->select('productos.*')
                  ->latest('productos.created_at');
        }

        $productos = $query->paginate(12)->withQueryString();

        return view('productos.marketplace', compact('productos', 'filtroActivo', 'radio', 'categoria', 'categorias'));
    }
}