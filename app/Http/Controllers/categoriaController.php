<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-categoria|crear-categoria|editar-categoria|eliminar-categoria', only: ['index']),
            new Middleware('permission:crear-categoria', only: ['create', 'store']),
            new Middleware('permission:editar-categoria', only: ['edit', 'update']),
            new Middleware('permission:eliminar-categoria', only: ['destroy']),
        ];
    }

    public function index()
    {
        $categorias = Categoria::withTrashed()->latest()->get();
        return view('categoria.index', compact('categorias'));
    }

    public function create()
    {
        return view('categoria.create');
    }

    public function store(StoreCategoriaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Categoria::create($request->validated());
            });

            return redirect()->route('categorias.index')->with('success', 'Categoría registrada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la categoría: ' . $e->getMessage()]);
        }
    }

    public function edit(Categoria $categoria)
    {
        return view('categoria.edit', compact('categoria'));
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        try {
            $categoria->update($request->validated());

            return redirect()->route('categorias.index')->with('success', 'Categoría editada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la categoría: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $categoria = Categoria::withTrashed()->findOrFail($id);

        try {
            if ($categoria->trashed()) {
                $categoria->restore();
                $message = 'Categoría restaurada';
            } else {
                $categoria->delete();
                $message = 'Categoría eliminada';
            }

            return redirect()->route('categorias.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la categoría: ' . $e->getMessage()]);
        }
    }
}