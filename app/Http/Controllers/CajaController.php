<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CajaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-caja|crear-caja|editar-caja|eliminar-caja', only: ['index']),
            new Middleware('permission:crear-caja', only: ['create', 'store']),
            new Middleware('permission:editar-caja', only: ['edit', 'update']),
            new Middleware('permission:eliminar-caja', only: ['destroy']),
        ];
    }

    public function index()
    {
        $cajas = Caja::withTrashed()->latest()->get();
        return view('caja.index', compact('cajas'));
    }

    public function create()
    {
        return view('caja.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50|unique:cajas,nombre',
            'fondo_fijo' => 'required|numeric|min:0',
            'estado' => 'nullable|boolean',
        ]);

        try {
            Caja::create([
                'nombre' => $data['nombre'],
                'fondo_fijo' => $data['fondo_fijo'],
                'estado' => $data['estado'] ?? 1,
            ]);

            return redirect()->route('cajas.index')->with('success', 'Caja registrada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la caja: ' . $e->getMessage()]);
        }
    }

    public function edit(Caja $caja)
    {
        return view('caja.edit', compact('caja'));
    }

    public function update(Request $request, Caja $caja)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50|unique:cajas,nombre,' . $caja->id,
            'fondo_fijo' => 'required|numeric|min:0',
            'estado' => 'required|boolean',
        ]);

        try {
            $caja->update($data);

            return redirect()->route('cajas.index')->with('success', 'Caja editada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la caja: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $caja = Caja::withTrashed()->findOrFail($id);

            if ($caja->trashed()) {
                $caja->restore();
                $message = 'Caja restaurada';
            } else {
                $caja->delete();
                $message = 'Caja eliminada';
            }

            return redirect()->route('cajas.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la caja: ' . $e->getMessage()]);
        }
    }
}