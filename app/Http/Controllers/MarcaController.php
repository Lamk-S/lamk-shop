<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarcaRequest;
use App\Http\Requests\UpdateMarcaRequest;
use App\Models\Marca;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MarcaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-marca|crear-marca|editar-marca|eliminar-marca', only: ['index']),
            new Middleware('permission:crear-marca', only: ['create', 'store']),
            new Middleware('permission:editar-marca', only: ['edit', 'update']),
            new Middleware('permission:eliminar-marca', only: ['destroy']),
        ];
    }

    public function index()
    {
        $marcas = Marca::withTrashed()->latest()->get();
        return view('marca.index', compact('marcas'));
    }

    public function create()
    {
        return view('marca.create');
    }

    public function store(StoreMarcaRequest $request)
    {
        try {
            Marca::create($request->validated());
            return redirect()->route('marcas.index')->with('success', 'Marca registrada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la marca: ' . $e->getMessage()]);
        }
    }

    public function edit(Marca $marca)
    {
        return view('marca.edit', compact('marca'));
    }

    public function update(UpdateMarcaRequest $request, Marca $marca)
    {
        try {
            $marca->update($request->validated());
            return redirect()->route('marcas.index')->with('success', 'Marca editada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la marca: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $marca = Marca::withTrashed()->findOrFail($id);

        try {
            if ($marca->trashed()) {
                $marca->restore();
                $message = 'Marca restaurada';
            } else {
                $marca->delete();
                $message = 'Marca eliminada';
            }

            return redirect()->route('marcas.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la marca: ' . $e->getMessage()]);
        }
    }
}