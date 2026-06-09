<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_productos', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $productos = Producto::with([
                'categorias',
                'marca',
                'variantes.talla',
            ])
            ->withTrashed()
            ->latest('id')
            ->get();

        return view('producto.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 1)->orderBy('nombre')->get();
        $marcas = Marca::where('estado', 1)->orderBy('nombre')->get();

        $tallasCalzado = Talla::where('estado', 1)
            ->where('tipo_talla', 'CALZADO')
            ->orderBy('orden')
            ->get();

        $tallasRopa = Talla::where('estado', 1)
            ->where('tipo_talla', 'ROPA')
            ->orderBy('orden')
            ->get();

        $tallaUnica = Talla::where('estado', 1)
            ->where('codigo', 'UNICA')
            ->first();

        $optionsTipoProducto = [
            'ZAPATILLA' => 'Zapatilla',
            'ROPA' => 'Ropa',
            'ACCESORIO' => 'Accesorio',
        ];

        return view('producto.create', compact(
            'categorias',
            'marcas',
            'tallasCalzado',
            'tallasRopa',
            'tallaUnica',
            'optionsTipoProducto'
        ));
    }

    public function store(StoreProductoRequest $request)
    {
        $data = $request->validated();

        if (in_array($data['tipo_producto'], ['ZAPATILLA', 'ROPA'], true) && !$request->boolean('maneja_tallas')) {
            throw ValidationException::withMessages([
                'maneja_tallas' => 'Las zapatillas y la ropa deportiva deben manejar tallas.',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $data) {
                $producto = new Producto();

                $imgPath = null;
                if ($request->hasFile('img_path')) {
                    $imgPath = $producto->handleUploadImage($request->file('img_path'));
                }

                $producto->fill([
                    'codigo' => $data['codigo'],
                    'codigo_barra' => $data['codigo_barra'] ?? null,
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'img_path' => $imgPath,
                    'tipo_producto' => $data['tipo_producto'],
                    'maneja_tallas' => $request->boolean('maneja_tallas'),
                    'precio_compra' => $data['precio_compra'],
                    'precio_venta' => $data['precio_venta'],
                    'stock_total' => 0,
                    'stock_minimo' => $data['stock_minimo'],
                    'afecto_igv' => $request->boolean('afecto_igv', true),
                    'marca_id' => $data['marca_id'] ?? null,
                    'estado' => 1,
                ]);

                $producto->save();

                $producto->categorias()->sync($data['categoria_id']);

                $this->syncVariantes($producto, $request);

                $producto->update([
                    'stock_total' => $producto->variantes()->where('estado', 1)->sum('stock_actual'),
                ]);
            });

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto registrado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar el producto: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function edit(Producto $producto)
    {
        $producto->load(['categorias', 'marca', 'variantes.talla']);

        $categorias = Categoria::where('estado', 1)->orderBy('nombre')->get();
        $marcas = Marca::where('estado', 1)->orderBy('nombre')->get();

        $tallasCalzado = Talla::where('estado', 1)
            ->where('tipo_talla', 'CALZADO')
            ->orderBy('orden')
            ->get();

        $tallasRopa = Talla::where('estado', 1)
            ->where('tipo_talla', 'ROPA')
            ->orderBy('orden')
            ->get();

        $tallaUnica = Talla::where('estado', 1)
            ->where('codigo', 'UNICA')
            ->first();

        $optionsTipoProducto = [
            'ZAPATILLA' => 'Zapatilla',
            'ROPA' => 'Ropa',
            'ACCESORIO' => 'Accesorio',
        ];

        return view('producto.edit', compact(
            'producto',
            'categorias',
            'marcas',
            'tallasCalzado',
            'tallasRopa',
            'tallaUnica',
            'optionsTipoProducto'
        ));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();

        if (in_array($data['tipo_producto'], ['ZAPATILLA', 'ROPA'], true) && !$request->boolean('maneja_tallas')) {
            throw ValidationException::withMessages([
                'maneja_tallas' => 'Las zapatillas y la ropa deportiva deben manejar tallas.',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $data, $producto) {
                $imgPath = $producto->img_path;

                if ($request->hasFile('img_path')) {
                    $newPath = $producto->handleUploadImage($request->file('img_path'));

                    if ($imgPath && Storage::disk('public')->exists($imgPath)) {
                        Storage::disk('public')->delete($imgPath);
                    }

                    $imgPath = $newPath;
                }

                $producto->update([
                    'codigo' => $data['codigo'],
                    'codigo_barra' => $data['codigo_barra'] ?? null,
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'img_path' => $imgPath,
                    'tipo_producto' => $data['tipo_producto'],
                    'maneja_tallas' => $request->boolean('maneja_tallas'),
                    'precio_compra' => $data['precio_compra'],
                    'precio_venta' => $data['precio_venta'],
                    'stock_minimo' => $data['stock_minimo'],
                    'afecto_igv' => $request->boolean('afecto_igv', true),
                    'marca_id' => $data['marca_id'] ?? null,
                ]);

                $producto->categorias()->sync($data['categoria_id']);

                $this->syncVariantes($producto, $request, true);

                $producto->update([
                    'stock_total' => $producto->variantes()->where('estado', 1)->sum('stock_actual'),
                ]);
            });

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto actualizado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al editar el producto: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $producto = Producto::withTrashed()->findOrFail($id);

            if ($producto->trashed()) {
                $producto->restore();
                $message = 'Producto restaurado correctamente';
            } else {
                $producto->delete();
                $message = 'Producto eliminado correctamente';
            }

            return redirect()
                ->route('productos.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al modificar el producto: ' . $e->getMessage(),
            ]);
        }
    }

    private function syncVariantes(Producto $producto, Request $request, bool $isUpdate = false): void
    {
        $tallaUnica = Talla::where('codigo', 'UNICA')->firstOrFail();

        $variantes = collect($request->input('variantes', []))
            ->filter(function ($variante) {
                return !empty($variante['talla_id']) || !empty($variante['stock_actual']) || !empty($variante['codigo_barra']);
            })
            ->values();

        if ($producto->maneja_tallas) {
            if ($variantes->isEmpty()) {
                throw ValidationException::withMessages([
                    'variantes' => 'Debes registrar al menos una variante de talla para este producto.',
                ]);
            }

            $this->syncProvidedVariants($producto, $variantes, $isUpdate);
            return;
        }

        $stockTotal = (int) $request->input('stock_total', 0);

        $variantesFinales = $variantes->isNotEmpty()
            ? $variantes
            : collect([
                [
                    'talla_id' => $tallaUnica->id,
                    'codigo_barra' => $request->input('codigo_barra'),
                    'stock_actual' => $stockTotal,
                    'stock_minimo' => $request->input('stock_minimo', 0),
                ],
            ]);

        $this->syncProvidedVariants($producto, $variantesFinales, $isUpdate);
    }

    private function syncProvidedVariants(Producto $producto, Collection $variantes, bool $isUpdate): void
    {
        $keptVariantIds = [];

        foreach ($variantes as $row) {
            $talla = Talla::findOrFail($row['talla_id']);

            $codigoVariante = $producto->codigo . '-' . $talla->codigo;

            $variantData = [
                'producto_id' => $producto->id,
                'talla_id' => $talla->id,
                'codigo_variante' => $codigoVariante,
                'codigo_barra' => $row['codigo_barra'] ?? null,
                'stock_actual' => (int) ($row['stock_actual'] ?? 0),
                'stock_minimo' => (int) ($row['stock_minimo'] ?? 0),
                'estado' => (int) ($row['estado'] ?? 1),
            ];

            $variante = ProductoVariante::withTrashed()->updateOrCreate(
                [
                    'producto_id' => $producto->id,
                    'talla_id' => $talla->id,
                ],
                $variantData
            );

            if ($variante->trashed()) {
                $variante->restore();
            }

            $keptVariantIds[] = $variante->id;
        }

        if ($isUpdate) {
            ProductoVariante::where('producto_id', $producto->id)
                ->whereNotIn('id', $keptVariantIds)
                ->get()
                ->each(function (ProductoVariante $variante) {
                    if (
                        $variante->kardex()->exists() ||
                        $variante->compraDetalles()->exists() ||
                        $variante->ventaDetalles()->exists()
                    ) {
                        $variante->update([
                            'estado' => 0,
                            'stock_actual' => 0,
                        ]);
                    } else {
                        $variante->delete();
                    }
                });
        }
    }
}