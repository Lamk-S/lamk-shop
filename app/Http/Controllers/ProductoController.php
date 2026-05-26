<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacion;
use App\Models\Producto;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto', only: ['index']),
            new Middleware('permission:crear-producto', only: ['create', 'store']),
            new Middleware('permission:editar-producto', only: ['edit', 'update']),
            new Middleware('permission:eliminar-producto', only: ['destroy']),
        ];
    }

    public function index()
    {
        $productos = Producto::with(['categorias', 'marca', 'presentacion'])
            ->latest()
            ->get();

        return view('producto.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 1)->get();
        $marcas = Marca::where('estado', 1)->get();
        $presentaciones = Presentacion::where('estado', 1)->get();

        return view('producto.create', compact('categorias', 'marcas', 'presentaciones'));
    }

    public function store(StoreProductoRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $producto = new Producto();

                $imgPath = null;
                if ($request->hasFile('img_path')) {
                    $imgPath = $producto->handleUploadImage($request->file('img_path'));
                }

                $producto->fill([
                    'codigo' => $request->codigo,
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'img_path' => $imgPath,
                    'precio_compra' => $request->precio_compra ?? 0,
                    'precio_venta' => $request->precio_venta ?? 0,
                    'stock' => $request->stock ?? 0,
                    'stock_minimo' => $request->stock_minimo ?? 5,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'estado' => 1,
                    'marca_id' => $request->marca_id,
                    'presentacion_id' => $request->presentacion_id,
                ]);

                $producto->save();

                $producto->categorias()->sync($request->get('categoria_id', []));
            });

            return redirect()->route('productos.index')->with('success', 'Producto registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el producto: ' . $e->getMessage()]);
        }
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('estado', 1)->get();
        $marcas = Marca::where('estado', 1)->get();
        $presentaciones = Presentacion::where('estado', 1)->get();

        $producto->load('categorias', 'marca', 'presentacion');

        return view('producto.edit', compact('producto', 'categorias', 'marcas', 'presentaciones'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try {
            DB::transaction(function () use ($request, $producto) {
                if ($request->hasFile('img_path')) {
                    $newPath = $producto->handleUploadImage($request->file('img_path'));

                    if ($producto->img_path && Storage::disk('public')->exists($producto->img_path)) {
                        Storage::disk('public')->delete($producto->img_path);
                    }
                } else {
                    $newPath = $producto->img_path;
                }

                $producto->update([
                    'codigo' => $request->codigo,
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'img_path' => $newPath,
                    'precio_compra' => $request->precio_compra ?? $producto->precio_compra,
                    'precio_venta' => $request->precio_venta ?? $producto->precio_venta,
                    'stock_minimo' => $request->stock_minimo ?? $producto->stock_minimo,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'marca_id' => $request->marca_id,
                    'presentacion_id' => $request->presentacion_id,
                ]);

                $producto->categorias()->sync($request->get('categoria_id', []));
            });

            return redirect()->route('productos.index')->with('success', 'Producto editado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el producto: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $producto = Producto::withTrashed()->findOrFail($id);

            if ($producto->trashed()) {
                $producto->restore();
                $message = 'Producto restaurado';
            } else {
                $producto->delete();
                $message = 'Producto eliminado';
            }

            return redirect()->route('productos.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el producto: ' . $e->getMessage()]);
        }
    }
}