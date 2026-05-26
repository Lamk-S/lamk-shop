<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresentacionRequest;
use App\Http\Requests\UpdatePresentacionRequest;
use App\Models\Presentacion;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PresentacionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-presentacion|crear-presentacion|editar-presentacion|eliminar-presentacion', only: ['index']),
            new Middleware('permission:crear-presentacion', only: ['create', 'store']),
            new Middleware('permission:editar-presentacion', only: ['edit', 'update']),
            new Middleware('permission:eliminar-presentacion', only: ['destroy']),
        ];
    }

    public function index()
    {
        $presentaciones = Presentacion::withTrashed()->latest()->get();
        return view('presentacion.index', compact('presentaciones'));
    }

    public function create()
    {
        return view('presentacion.create');
    }

    public function store(StorePresentacionRequest $request)
    {
        try {
            Presentacion::create($request->validated());
            return redirect()->route('presentaciones.index')->with('success', 'Presentación registrada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la presentación: ' . $e->getMessage()]);
        }
    }

    public function edit(Presentacion $presentacion)
    {
        return view('presentacion.edit', compact('presentacion'));
    }

    public function update(UpdatePresentacionRequest $request, Presentacion $presentacion)
    {
        try {
            $presentacion->update($request->validated());
            return redirect()->route('presentaciones.index')->with('success', 'Presentación editada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la presentación: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $presentacion = Presentacion::withTrashed()->findOrFail($id);

        try {
            if ($presentacion->trashed()) {
                $presentacion->restore();
                $message = 'Presentación restaurada';
            } else {
                $presentacion->delete();
                $message = 'Presentación eliminada';
            }

            return redirect()->route('presentaciones.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la presentación: ' . $e->getMessage()]);
        }
    }
}