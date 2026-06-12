<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CajaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_cajas|abrir_caja|cerrar_caja|movimientos_caja', only: ['index']),
            new Middleware('permission:gestionar_cajas', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $cajas = Caja::withTrashed()->latest('id')->get();

        return view('caja.index', compact('cajas'));
    }

    public function create()
    {
        return view('caja.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:cajas,codigo'],
            'nombre' => ['required', 'string', 'max:50'],
            'fondo_fijo' => ['required', 'numeric', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ]);

        try {
            Caja::create([
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'fondo_fijo' => $data['fondo_fijo'],
                'estado' => $data['estado'] ?? 1,
            ]);

            return redirect()->route('cajas.index')->with('success', 'Caja registrada correctamente');
        } catch (\Exception $e) {
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
            'codigo' => ['required', 'string', 'max:20', 'unique:cajas,codigo,' . $caja->id],
            'nombre' => ['required', 'string', 'max:50'],
            'fondo_fijo' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'boolean'],
        ]);

        try {
            $caja->update($data);

            return redirect()->route('cajas.index')->with('success', 'Caja editada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la caja: ' . $e->getMessage()]);
        }
    }

    public function destroy(Caja $caja)
    {
        try {
            if ($caja->sesionesCaja()->where('estado_sesion', 'ABIERTA')->exists()) {
                return back()->withErrors(['error' => 'No se puede eliminar una caja con una sesión abierta.']);
            }

            if ($caja->trashed()) {
                $caja->restore();
                $message = 'Caja restaurada correctamente';
            } else {
                $caja->delete();
                $message = 'Caja eliminada correctamente';
            }

            return redirect()->route('cajas.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la caja: ' . $e->getMessage()]);
        }
    }
}