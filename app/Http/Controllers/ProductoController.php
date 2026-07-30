<?php

namespace App\Http\Controllers;

use App\Enums\TipoProducto;
use App\Enums\TipoTalla;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller implements HasMiddleware
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
        $query = Producto::with([
                'categorias:id,nombre',
                'marca:id,nombre',
                'variantes.talla:id,codigo,nombre',
            ])
            ->withTrashed()
            ->latest('id');

        $query->when($request->filled('q'), function ($q) use ($request) {
            $search = trim((string) $request->input('q'));
            $q->where(function ($subQ) use ($search) {
                $subQ->where('codigo', 'like', "%{$search}%")
                     ->orWhere('nombre', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('tipo_producto'), fn($q) => $q->where('tipo_producto', $request->input('tipo_producto')))
        ->when($request->filled('marca_id'), fn($q) => $q->where('marca_id', $request->input('marca_id')))
        ->when($request->filled('estado'), function ($q) use ($request) {
            if ($request->input('estado') === 'activo') {
                $q->where('estado', 1)->whereNull('deleted_at');
            } elseif ($request->input('estado') === 'inactivo') {
                $q->where(fn($sub) => $sub->where('estado', 0)->orWhereNotNull('deleted_at'));
            }
        });

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $productos = $query->paginate($perPage)->withQueryString();

        $marcas = Marca::where('estado', 1)->orderBy('nombre')->get();

        return view('producto.index', compact('productos', 'perPage', 'marcas'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 1)->orderBy('nombre')->get();
        $marcas = Marca::where('estado', 1)->orderBy('nombre')->get();

        $tallasCalzado = Talla::where('estado', 1)
            ->where('tipo_talla', TipoTalla::CALZADO)
            ->orderBy('orden')
            ->get();

        $tallasRopa = Talla::where('estado', 1)
            ->where('tipo_talla', TipoTalla::ROPA)
            ->orderBy('orden')
            ->get();

        $tallaUnica = Talla::where('estado', 1)
            ->where('codigo', Talla::CODIGO_UNICA)
            ->first();

        $optionsTipoProducto = TipoProducto::opciones();

        $editing = false;

        $tipoProductoActual = old('tipo_producto', '');
        $afectoIgvActual = old('afecto_igv', '1');
        $manejaTallasActual = old('maneja_tallas', '0');
        $selectedCategorias = old('categoria_id', []);

        $variantRows = [[
            'id' => null,
            'talla_id' => '',
            'codigo_variante' => '',
            'stock_actual' => 0,
            'stock_minimo' => 0,
            'estado' => 1,
        ]];

        $reglasTallas = json_encode([
            'ZAPATILLA' => ['CALZADO'],
            'ROPA'      => ['ROPA'],
            'ACCESORIO' => ['UNICA'],
        ]);

        return view('producto.create', compact(
            'categorias', 'marcas', 'tallasCalzado', 'tallasRopa',
            'tallaUnica', 'optionsTipoProducto', 'editing', 'tipoProductoActual',
            'afectoIgvActual', 'manejaTallasActual', 'selectedCategorias',
            'variantRows', 'reglasTallas'
        ));
    }

    public function store(StoreProductoRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($request, $data) {
                $producto = new Producto();
                $imgPath = null;

                if ($request->hasFile('img_path')) {
                    $imgPath = $producto->handleUploadImage($request->file('img_path'));
                }

                $producto->fill([
                    'codigo' => $data['codigo'],
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
                    'estado' => 1,
                ]);

                $producto->save();
                $producto->categorias()->sync($data['categoria_id']);
                $this->syncVariantes($producto, $request);
            });

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto registrado correctamente');
        } catch (\Exception $e) {
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
        $tallasCalzado = Talla::where('estado', 1)->where('tipo_talla', TipoTalla::CALZADO)->orderBy('orden')->get();
        $tallasRopa = Talla::where('estado', 1)->where('tipo_talla', TipoTalla::ROPA)->orderBy('orden')->get();
        $tallaUnica = Talla::where('estado', 1)->where('codigo', Talla::CODIGO_UNICA)->first();

        $optionsTipoProducto = TipoProducto::opciones();

        $editing = true;
        $tipoProductoActual = old('tipo_producto', $producto->tipo_producto?->value ?? $producto->tipo_producto);
        $afectoIgvActual = old('afecto_igv', $producto->afecto_igv ? '1' : '0');
        $manejaTallasActual = old('maneja_tallas', $producto->maneja_tallas ? '1' : '0');
        $selectedCategorias = old('categoria_id', $producto->categorias->pluck('id')->toArray());

        $variantRows = $producto->variantes->map(function ($v) {
            return [
                'id' => $v->id,
                'talla_id' => $v->talla_id,
                'codigo_barra' => $v->codigo_barra ?? '',
                'stock_actual' => $v->stock_actual,
                'stock_minimo' => $v->stock_minimo,
                'estado' => $v->estado,
            ];
        })->toArray();

        if (empty($variantRows)) {
            $variantRows = [[
                'id' => null,
                'talla_id' => '',
                'codigo_barra' => '',
                'stock_actual' => 0,
                'stock_minimo' => 0,
                'estado' => 1,
            ]];
        }

        $reglasTallas = json_encode([
            'ZAPATILLA' => ['CALZADO'],
            'ROPA'      => ['ROPA'],
            'ACCESORIO' => ['UNICA'],
        ]);

        return view('producto.edit', compact(
            'producto', 'categorias', 'marcas', 
            'tallasCalzado', 'tallasRopa', 'tallaUnica', 
            'optionsTipoProducto', 'editing', 'tipoProductoActual', 
            'afectoIgvActual', 'manejaTallasActual', 'selectedCategorias', 
            'variantRows', 'reglasTallas'
        ));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();

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
            });

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto actualizado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al editar el producto: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            $producto->update([
                'estado' => 0
            ]);
            $producto->delete();
            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al eliminar producto: '.$e->getMessage(),
            ]);
        }
    }

    public function restore(int $id)
    {
        try {
            $producto = Producto::withTrashed()
                ->findOrFail($id);
            $producto->restore();
            $producto->update([
                'estado' => 1
            ]);
            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto restaurado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al restaurar producto: '.$e->getMessage(),
            ]);
        }
    }

    private function syncVariantes(Producto $producto, Request $request, bool $isUpdate = false): void
    {
        $tallaUnica = Talla::where('codigo', Talla::CODIGO_UNICA)->firstOrFail();

        $variantes = collect($request->input('variantes', []))
            ->filter(function ($variante) {
                return !empty($variante['talla_id']) || !empty($variante['stock_actual']) || !empty($variante['codigo_variante']);
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

        $stockActual = (int) ($variantes->first()['stock_actual'] ?? 0);
        $stockMinimo = (int) ($variantes->first()['stock_minimo'] ?? 0);

        $variantesFinales = collect([
            [
                'talla_id' => $tallaUnica->id,
                'codigo_variante' => ProductoVariante::generarCodigoVariante($producto, $tallaUnica),
                'stock_actual' => $stockActual,
                'stock_minimo' => $stockMinimo,
            ],
        ]);

        $this->syncProvidedVariants($producto, $variantesFinales, $isUpdate);
    }

    private function syncProvidedVariants(Producto $producto, Collection $variantes, bool $isUpdate): void
    {
        $keptVariantIds = [];

        foreach ($variantes as $row) {
            $talla = Talla::findOrFail($row['talla_id']);
            $codigoVariante = ProductoVariante::generarCodigoVariante($producto, $talla);

            $variantData = [
                'producto_id' => $producto->id,
                'talla_id' => $talla->id,
                'codigo_variante' => $codigoVariante,
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