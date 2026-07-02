<?php

namespace App\Http\Controllers;

use App\Enums\TipoProducto;
use App\Http\Requests\StoreProductoVarianteRequest;
use App\Http\Requests\UpdateProductoVarianteRequest;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductoVarianteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_productos', only: ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = ProductoVariante::with(['producto.marca', 'talla'])
            ->withTrashed()
            ->latest('id');

        $query->when($request->filled('q'), function ($q) use ($request) {
            $search = trim((string) $request->input('q'));
            $q->where(function ($sub) use ($search) {
                $sub->where('codigo_variante', 'like', "%{$search}%")
                    ->orWhereHas('producto', fn($qp) => $qp->where('codigo', 'like', "%{$search}%")->orWhere('nombre', 'like', "%{$search}%"))
                    ->orWhereHas('talla', fn($qt) => $qt->where('codigo', 'like', "%{$search}%")->orWhere('nombre', 'like', "%{$search}%"));
            });
        })
            ->when($request->filled('producto_id'), fn($q) => $q->where('producto_id', $request->producto_id))
            ->when($request->filled('talla_id'), fn($q) => $q->where('talla_id', $request->talla_id))
            ->when($request->filled('estado'), function ($q) use ($request) {
                match ($request->estado) {
                    'activo' => $q->where('estado', 1)->whereNull('deleted_at'),
                    'inactivo' => $q->where(fn($sub) => $sub->where('estado', 0)->orWhereNotNull('deleted_at')),
                    default => $q,
                };
            });

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $variantes = $query->paginate($perPage)->withQueryString();

        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'tipo_producto']);

        $tallas = Talla::where('estado', 1)
            ->orderBy('tipo_talla')
            ->orderBy('orden')
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'nombre', 'tipo_talla'])
            ->unique('id')
            ->values();

        return view('producto_variante.index', compact('variantes', 'productos', 'tallas', 'perPage'));
    }

    public function create()
    {
        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'tipo_producto'])
            ->values();

        $tallas = Talla::where('estado', 1)
            ->orderBy('tipo_talla')
            ->orderBy('orden')
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'nombre', 'tipo_talla'])
            ->unique('id')
            ->values();

        return view('producto_variante.create', compact('productos', 'tallas'));
    }

    public function store(StoreProductoVarianteRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $producto = Producto::findOrFail($data['producto_id']);
                $talla = Talla::findOrFail($data['talla_id']);

                $this->validarCompatibilidadProductoTalla($producto, $talla);

                $existing = ProductoVariante::withTrashed()
                    ->where('producto_id', $producto->id)
                    ->where('talla_id', $talla->id)
                    ->first();

                if ($existing && !$existing->trashed()) {
                    throw ValidationException::withMessages([
                        'talla_id' => 'Ya existe una variante activa para este producto y esta talla.',
                    ]);
                }

                $variantCode = $producto->codigo . '-' . $talla->codigo;

                if ($existing && $existing->trashed()) {
                    $existing->restore();
                    $existing->update([
                        'codigo_variante' => $variantCode,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'costo_ultimo_compra' => $data['costo_ultimo_compra'] ?? 0,
                        'costo_promedio' => $data['costo_promedio'] ?? 0,
                        'ultima_compra_at' => $data['ultima_compra_at'] ?? null,
                        'estado' => $data['estado'] ?? 1,
                    ]);
                } else {
                    ProductoVariante::create([
                        'producto_id' => $producto->id,
                        'talla_id' => $talla->id,
                        'codigo_variante' => $variantCode,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'costo_ultimo_compra' => $data['costo_ultimo_compra'] ?? 0,
                        'costo_promedio' => $data['costo_promedio'] ?? 0,
                        'ultima_compra_at' => $data['ultima_compra_at'] ?? null,
                        'estado' => $data['estado'] ?? 1,
                    ]);
                }
            });

            return redirect()->route('producto-variantes.index')->with('success', 'Variante registrada correctamente');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['error' => 'Error al registrar la variante.'])
                ->withInput();
        }
    }

    public function show(ProductoVariante $producto_variante)
    {
        $productoVariante = $producto_variante->load(['producto.marca', 'talla']);
        return view('producto_variante.show', compact('productoVariante'));
    }

    public function edit(ProductoVariante $producto_variante)
    {
        $productoVariante = $producto_variante->load(['producto.marca', 'talla']);

        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'tipo_producto'])
            ->values();

        $tallas = Talla::where('estado', 1)
            ->orderBy('tipo_talla')
            ->orderBy('orden')
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'nombre', 'tipo_talla'])
            ->unique('id')
            ->values();

        return view('producto_variante.edit', compact('productoVariante', 'productos', 'tallas'));
    }

    public function update(UpdateProductoVarianteRequest $request, ProductoVariante $producto_variante)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $producto_variante) {
                $producto = Producto::findOrFail($data['producto_id']);
                $talla = Talla::findOrFail($data['talla_id']);

                $this->validarCompatibilidadProductoTalla($producto, $talla);

                $duplicate = ProductoVariante::withTrashed()
                    ->where('producto_id', $producto->id)
                    ->where('talla_id', $talla->id)
                    ->where('id', '!=', $producto_variante->id)
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'talla_id' => 'Ya existe otra variante para este producto y esta talla.',
                    ]);
                }

                if ($producto_variante->trashed()) {
                    $producto_variante->restore();
                }

                $producto_variante->update([
                    'producto_id' => $producto->id,
                    'talla_id' => $talla->id,
                    'codigo_variante' => ProductoVariante::generarCodigoVariante($producto, $talla),
                    'stock_actual' => $data['stock_actual'],
                    'stock_minimo' => $data['stock_minimo'],
                    'costo_ultimo_compra' => $data['costo_ultimo_compra'] ?? $producto_variante->costo_ultimo_compra,
                    'costo_promedio' => $data['costo_promedio'] ?? $producto_variante->costo_promedio,
                    'ultima_compra_at' => $data['ultima_compra_at'] ?? $producto_variante->ultima_compra_at,
                    'estado' => $data['estado'],
                ]);
            });

            return redirect()->route('producto-variantes.index')->with('success', 'Variante actualizada correctamente');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['error' => 'Error al actualizar la variante.'])
                ->withInput();
        }
    }

    public function destroy(ProductoVariante $producto_variante)
    {
        try {
            if ($producto_variante->trashed()) {
                $producto_variante->restore();
                $producto_variante->update(['estado' => 1]);
                $message = 'Variante restaurada correctamente';
            } else {
                $producto_variante->delete();
                $message = 'Variante eliminada correctamente';
            }

            return redirect()->route('producto-variantes.index')->with('success', $message);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['error' => 'Error al modificar la variante.']);
        }
    }

    private function validarCompatibilidadProductoTalla(Producto $producto, Talla $talla): void
    {
        if ($producto->tipo_producto === TipoProducto::ACCESORIO && $talla->tipo_talla !== Talla::TIPO_UNICA) {
            throw ValidationException::withMessages([
                'talla_id' => 'Los accesorios deben usar talla única.',
            ]);
        }

        if (in_array($producto->tipo_producto, [TipoProducto::ZAPATILLA, TipoProducto::ROPA], true) && $talla->tipo_talla === Talla::TIPO_UNICA) {
            throw ValidationException::withMessages([
                'talla_id' => 'Las zapatillas y la ropa no pueden usar talla única.',
            ]);
        }
    }
}
