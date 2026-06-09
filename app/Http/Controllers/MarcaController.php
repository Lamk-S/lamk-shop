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
            new Middleware('permission:gestionar_marcas', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $marcas = Marca::withTrashed()->latest('id')->get();

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

            return redirect()->route('marcas.index')->with('success', 'Marca registrada correctamente');
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

            return redirect()->route('marcas.index')->with('success', 'Marca editada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la marca: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $marca = Marca::withTrashed()->findOrFail($id);

            if ($marca->trashed()) {
                $marca->restore();
                $message = 'Marca restaurada correctamente';
            } else {
                $marca->delete();
                $message = 'Marca eliminada correctamente';
            }

            return redirect()->route('marcas.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la marca: ' . $e->getMessage()]);
        }
    }
}