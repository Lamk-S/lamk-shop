<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_categorias', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Categoria::query()
            ->withTrashed()
            ->latest('id');

        $query->when($request->filled('q'), function ($q) use ($request) {
            $search = trim((string) $request->input('q'));
            $q->where(fn($sub) => $sub->where('nombre', 'like', "%{$search}%")->orWhere('descripcion', 'like', "%{$search}%"));
        })
        ->when($request->filled('estado'), function ($q) use ($request) {
            match ($request->input('estado')) {
                'activa' => $q->where('estado', 1)->whereNull('deleted_at'),
                'inactiva' => $q->where('estado', 0)->whereNull('deleted_at'),
                'eliminada' => $q->onlyTrashed(),
                default => $q,
            };
        });

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $categorias = $query->paginate($perPage)->withQueryString();

        return view('categoria.index', compact('categorias', 'perPage'));
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

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría registrada correctamente');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al registrar la categoría: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Categoria $categoria)
    {
        return view('categoria.edit', compact('categoria'));
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        try {
            DB::transaction(function () use ($request, $categoria) {
                $categoria->update($request->validated());
            });

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría editada correctamente');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al editar la categoría: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Categoria $categoria)
    {
        try {
            $categoria->delete();
            return back()->with('success', 'Categoría eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }

    public function restore(Categoria $categoria)
    {
        try {
            $categoria->restore();
            return back()->with('success', 'Categoría restaurada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar la categoría: ' . $e->getMessage());
        }
    }

    public function forceDelete(Categoria $categoria)
    {
        try {
            $categoria->forceDelete();
            return back()->with('success', 'Categoría eliminada permanentemente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }
}