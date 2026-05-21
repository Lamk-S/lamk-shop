<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compras;
use App\Models\Comprobante;
use App\Models\Producto;
use App\Models\Proveedore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('permission:ver-compra|crear-compra|mostrar-compra|eliminar-compra', only: ['index']),
            new Middleware('permission:crear-compra', only: ['create', 'store']),
            new Middleware('permission:mostrar-compra', only: ['show']),
            new Middleware('permission:eliminar-compra', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = Compras::with('comprobante', 'proveedore.persona')
        ->latest()
        ->get();
        return view('compra.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proveedores = Proveedore::whereHas('persona', function($query) {
            $query->where('estado', 1);
        })->get();
        $comprobantes = Comprobante::all();
        $productos = Producto::where('estado', 1)->get();
        return view('compra.create', compact('proveedores', 'comprobantes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompraRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Tabla compras
            $compra = Compras::create($request->validated());

            // Llenar tabla compra_producto
            // 1. Recuperar los arrays
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioCompra = $request->get('arraypreciocompra');
            $arrayPrecioVenta = $request->get('arrayprecioventa');

            // 2. Realizar el llenado
            $siseArray = count($arrayProducto_id);
            $cont = 0;
            while ($cont < $siseArray) {
                $compra->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'cantidad' => $arrayCantidad[$cont],
                        'precio_compra' => $arrayPrecioCompra[$cont],
                        'precio_venta' => $arrayPrecioVenta[$cont]
                    ]
                ]);

                $producto = Producto::find($arrayProducto_id[$cont]);

                $stockActual = $producto->stock;
                $stockNuevo = intval($arrayCantidad[$cont]);

                DB::table('productos')
                    ->where('id', $producto->id)
                    ->update([
                        'stock' => $stockActual + $stockNuevo
                    ]);

                $cont++;
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

        return redirect()->route('compras.index')->with('success', 'Compra exitosa');
    }

    /**
     * Display the specified resource.
     */
    public function show(Compras $compra)
    {
        return view('compra.show', compact('compra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $compra = Compras::find($id);
        if($compra->estado == 1) {
            Compras::where('id', $id)
            ->update([
                'estado' => 0
            ]);
            $message = 'Compra eliminada';
        } else {
            Compras::where('id', $id)
            ->update([
                'estado' => 1
            ]);
            $message = 'Compra restaurada';
        }

        return redirect()->route('compras.index')->with('success', $message);
    }
}
