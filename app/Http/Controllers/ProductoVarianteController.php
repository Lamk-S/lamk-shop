<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoVarianteRequest;
use App\Http\Requests\UpdateProductoVarianteRequest;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
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
        $variantes = ProductoVariante::with(['producto.marca', 'talla'])
            ->withTrashed()
            ->latest('id')
            ->get();

        return view('producto_variante.index', compact('variantes'));
    }

    public function create()
    {
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();
        $tallas = Talla::where('estado', 1)->orderBy('tipo_talla')->orderBy('orden')->get();

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
                        'codigo_barra' => $data['codigo_barra'] ?? null,
                        'stock_actual' => $data['stock_actual'],
                        'stock_minimo' => $data['stock_minimo'],
                        'costo_ultimo_compra' => $data['costo_ultimo_compra'] ?? 0,
                        'costo_promedio' => $data['costo_promedio'] ?? 0,
                        'ultima_compra_at' => $data['ultima_compra_at'] ?? null,
                        'estado' => $data['estado'] ?? 1,
                    ]);
                }
            });

            return redirect()->route('productos.index')->with('success', 'Variante registrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar la variante: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function edit(ProductoVariante $producto_variante)
    {
        $productoVariante = $producto_variante->load(['producto', 'talla']);
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();
        $tallas = Talla::where('estado', 1)->orderBy('tipo_talla')->orderBy('orden')->get();

        $optionsTipoProducto = [
            'ZAPATILLA' => 'Zapatilla',
            'ROPA' => 'Ropa',
            'ACCESORIO' => 'Accesorio',
        ];

        return view('producto_variante.edit', compact('productoVariante', 'productos', 'tallas', 'optionsTipoProducto'));
    }

    public function update(UpdateProductoVarianteRequest $request, ProductoVariante $producto_variante)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $producto_variante) {
                $producto = Producto::findOrFail($data['producto_id']);
                $talla = Talla::findOrFail($data['talla_id']);

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
                    'codigo_variante' => $producto->codigo . '-' . $talla->codigo,
                    'codigo_barra' => $data['codigo_barra'] ?? null,
                    'stock_actual' => $data['stock_actual'],
                    'stock_minimo' => $data['stock_minimo'],
                    'costo_ultimo_compra' => $data['costo_ultimo_compra'] ?? $producto_variante->costo_ultimo_compra,
                    'costo_promedio' => $data['costo_promedio'] ?? $producto_variante->costo_promedio,
                    'ultima_compra_at' => $data['ultima_compra_at'] ?? $producto_variante->ultima_compra_at,
                    'estado' => $data['estado'],
                ]);
            });

            return redirect()->route('productos.index')->with('success', 'Variante actualizada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar la variante: ' . $e->getMessage(),
            ])->withInput();
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

            return redirect()->route('productos.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al modificar la variante: ' . $e->getMessage(),
            ]);
        }
    }
}