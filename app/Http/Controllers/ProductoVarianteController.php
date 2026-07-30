<?php

namespace App\Http\Controllers;

use App\Enums\TipoProducto;
use App\Enums\TipoTalla;
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
            new Middleware('permission:gestionar_productos|ver_productos', only: ['index', 'show']),
            new Middleware('permission:gestionar_productos', only: ['create', 'store', 'edit', 'update', 'destroy']),
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

        $perPage = in_array((int) $request->input('per_page', 15), [10, 15, 25, 50, 100], true) ? (int) $request->input('per_page', 15) : 15;

        $variantes = $query->paginate($perPage)->withQueryString();
        $dependencies = $this->getFormDependencies();

        return view('producto_variante.index', array_merge(compact('variantes', 'perPage'), $dependencies));
    }

    public function create()
    {
       $productoVariante = new ProductoVariante();

        return view(
            'producto_variante.create', array_merge(compact('productoVariante'), $this->getFormDependencies()));
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

                $variantCode = ProductoVariante::generarCodigoVariante($producto, $talla);

                if ($existing && $existing->trashed()) {
                    $existing->restore();
                    $existing->update([
                        'codigo_variante' => $variantCode,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'estado' => $data['estado'] ?? 1,
                    ]);
                } else {
                    ProductoVariante::create([
                        'producto_id' => $producto->id,
                        'talla_id' => $talla->id,
                        'codigo_variante' => $variantCode,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'estado' => $data['estado'] ?? 1,
                    ]);
                }
            });

            return redirect()->route('producto-variantes.index')->with('success', 'Variante registrada correctamente');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['error' => 'Error al registrar la variante.'])->withInput();
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
        $dependencies = $this->getFormDependencies();

        return view('producto_variante.edit', array_merge(compact('productoVariante'), $dependencies));
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
                    'estado' => $data['estado'],
                ]);
            });

            return redirect()->route('producto-variantes.index')->with('success', 'Variante actualizada correctamente');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['error' => 'Error al actualizar la variante.'])->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $producto_variante = ProductoVariante::withTrashed()->findOrFail($id);

            if ($producto_variante->trashed()) {
                $producto_variante->restore();
                $producto_variante->update(['estado' => 1]);
                $message = 'Variante restaurada y activada correctamente';
            } else {
                $producto_variante->delete();
                $message = 'Variante desactivada correctamente';
            }

            return redirect()->route('producto-variantes.index')->with('success', $message);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['error' => 'Error al modificar el estado de la variante.']);
        }
    }

    private function validarCompatibilidadProductoTalla(Producto $producto, Talla $talla): void
    {
        if ($producto->tipo_producto === TipoProducto::ACCESORIO && $talla->tipo_talla !== TipoTalla::UNICA) {
            throw ValidationException::withMessages(['talla_id' => 'Los accesorios deben usar talla única.']);
        }
        if (in_array($producto->tipo_producto, [TipoProducto::ZAPATILLA, TipoProducto::ROPA], true) && $talla->tipo_talla === TipoTalla::UNICA) {
            throw ValidationException::withMessages(['talla_id' => 'Las zapatillas y la ropa no pueden usar talla única.']);
        }
    }

    private function getFormDependencies(): array
    {
        return [
            'productos' => Producto::where('estado', 1)->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'tipo_producto'])->values(),
            'tallas' => Talla::where('estado', 1)->orderBy('tipo_talla')->orderBy('orden')->orderBy('codigo')->get(['id', 'codigo', 'nombre', 'tipo_talla'])->unique('id')->values(),
            'reglasTallas' => json_encode([
                'ZAPATILLA' => ['CALZADO'],
                'ROPA'      => ['ROPA'],
                'ACCESORIO' => ['UNICA'],
            ])
        ];
    }
}