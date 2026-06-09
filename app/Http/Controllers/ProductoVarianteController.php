<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoVarianteRequest;
use App\Http\Requests\UpdateProductoVarianteRequest;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductoVarianteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_productos', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $variantes = ProductoVariante::with([
                'producto.marca',
                'talla',
            ])
            ->withTrashed()
            ->latest('id')
            ->get();

        return view('producto_variante.index', compact('variantes'));
    }

    public function create()
    {
        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $tallas = Talla::where('estado', 1)
            ->orderBy('tipo_talla')
            ->orderBy('orden')
            ->get();

        $optionsTipoProducto = [
            'ZAPATILLA' => 'Zapatilla',
            'ROPA' => 'Ropa',
            'ACCESORIO' => 'Accesorio',
        ];

        return view('producto_variante.create', compact('productos', 'tallas', 'optionsTipoProducto'));
    }

    public function store(StoreProductoVarianteRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $producto = Producto::findOrFail($data['producto_id']);
                $talla = Talla::findOrFail($data['talla_id']);

                $this->validateBusinessRules($producto, $talla);

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
                        'codigo_barra' => $data['codigo_barra'] ?? null,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'estado' => $data['estado'] ?? 1,
                    ]);
                } else {
                    ProductoVariante::create([
                        'producto_id' => $producto->id,
                        'talla_id' => $talla->id,
                        'codigo_variante' => $variantCode,
                        'codigo_barra' => $data['codigo_barra'] ?? null,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'estado' => $data['estado'] ?? 1,
                    ]);
                }

                $this->recalculateProductStock($producto->id);
            });

            return redirect()
                ->route('productos.index')
                ->with('success', 'Variante registrada correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar la variante: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function edit(string $id)
    {
        $productoVariante = ProductoVariante::withTrashed()
            ->with(['producto', 'talla'])
            ->findOrFail($id);

        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $tallas = Talla::where('estado', 1)
            ->orderBy('tipo_talla')
            ->orderBy('orden')
            ->get();

        $optionsTipoProducto = [
            'ZAPATILLA' => 'Zapatilla',
            'ROPA' => 'Ropa',
            'ACCESORIO' => 'Accesorio',
        ];

        return view('producto_variante.edit', compact('productoVariante', 'productos', 'tallas', 'optionsTipoProducto'));
    }

    public function update(UpdateProductoVarianteRequest $request, string $id)
    {
        $productoVariante = ProductoVariante::withTrashed()->findOrFail($id);

        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $productoVariante) {
                $producto = Producto::findOrFail($data['producto_id']);
                $talla = Talla::findOrFail($data['talla_id']);

                $this->validateBusinessRules($producto, $talla);

                $variantCode = $producto->codigo . '-' . $talla->codigo;

                $duplicate = ProductoVariante::withTrashed()
                    ->where('producto_id', $producto->id)
                    ->where('talla_id', $talla->id)
                    ->where('id', '!=', $productoVariante->id)
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'talla_id' => 'Ya existe otra variante para este producto y esta talla.',
                    ]);
                }

                if ($productoVariante->trashed()) {
                    $productoVariante->restore();
                }

                $productoVariante->update([
                    'producto_id' => $producto->id,
                    'talla_id' => $talla->id,
                    'codigo_variante' => $variantCode,
                    'codigo_barra' => $data['codigo_barra'] ?? null,
                    'stock_actual' => $data['stock_actual'],
                    'stock_minimo' => $data['stock_minimo'],
                    'estado' => $data['estado'],
                ]);

                $this->recalculateProductStock($producto->id);
            });

            return redirect()
                ->route('productos.index')
                ->with('success', 'Variante actualizada correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar la variante: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $message = '';
            $productoVariante = ProductoVariante::withTrashed()->findOrFail($id);

            DB::transaction(function () use ($productoVariante) {
                $productoId = $productoVariante->producto_id;

                if ($productoVariante->trashed()) {
                    $productoVariante->restore();
                    $productoVariante->update(['estado' => 1]);
                    $message = 'Variante restaurada correctamente';
                } else {
                    $productoVariante->delete();
                    $message = 'Variante eliminada correctamente';
                }

                $this->recalculateProductStock($productoId);
            });

            return redirect()
                ->route('productos.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al modificar la variante: ' . $e->getMessage(),
            ]);
        }
    }

    private function validateBusinessRules(Producto $producto, Talla $talla): void
    {
        if (in_array($producto->tipo_producto, ['ZAPATILLA', 'ROPA'], true) && $talla->codigo === 'UNICA') {
            throw ValidationException::withMessages([
                'talla_id' => 'Las zapatillas y la ropa deportiva no pueden usar talla única.',
            ]);
        }

        if ($producto->tipo_producto === 'ACCESORIO' && $talla->codigo !== 'UNICA') {
            throw ValidationException::withMessages([
                'talla_id' => 'Los accesorios deben manejar talla única.',
            ]);
        }
    }

    private function recalculateProductStock(int $productoId): void
    {
        $producto = Producto::find($productoId);

        if (!$producto) {
            return;
        }

        $total = (int) ProductoVariante::where('producto_id', $productoId)
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->sum('stock_actual');

        $producto->update([
            'stock_total' => $total,
        ]);
    }
}