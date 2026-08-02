<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarcaRequest;
use App\Http\Requests\UpdateMarcaRequest;
use App\Models\Marca;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_marcas', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Marca::query()
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

        $marcas = $query->paginate($perPage)->withQueryString();

        return view('marca.index', compact('marcas', 'perPage'));
    }

    public function create()
    {
        return view('marca.create');
    }

    public function store(StoreMarcaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Marca::create($request->validated());
            });

            return redirect()
                ->route('marcas.index')
                ->with('success', 'Marca registrada correctamente');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al registrar la marca: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Marca $marca)
    {
        return view('marca.edit', compact('marca'));
    }

    public function update(UpdateMarcaRequest $request, Marca $marca)
    {
        try {
            DB::transaction(function () use ($request, $marca) {
                $marca->update($request->validated());
            });

            return redirect()
                ->route('marcas.index')
                ->with('success', 'Marca editada correctamente');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al editar la marca: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Marca $marca)
    {
        try {
            $marca->delete();
            return back()->with('success', 'Marca eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la marca: ' . $e->getMessage());
        }
    }

    public function restore(Marca $marca)
    {
        try {
            $marca->restore();
            return back()->with('success', 'Marca restaurada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar la marca.');
        }
    }

    public function forceDelete(Marca $marca)
    {
        try {
            $marca->forceDelete();
            return back()->with('success', 'Marca eliminada permanentemente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }
}