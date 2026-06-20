<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTallaRequest;
use App\Http\Requests\UpdateTallaRequest;
use App\Models\Talla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class TallaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_tallas', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Talla::query()
            ->withTrashed()
            ->latest('id');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('tipo_talla', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_talla')) {
            $query->where('tipo_talla', $request->input('tipo_talla'));
        }

        if ($request->filled('estado')) {
            match ($request->input('estado')) {
                'activa' => $query->where('estado', 1)->whereNull('deleted_at'),
                'inactiva' => $query->where('estado', 0)->whereNull('deleted_at'),
                'eliminada' => $query->onlyTrashed(),
                default => null,
            };
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $tallas = $query->paginate($perPage)->withQueryString();

        return view('talla.index', compact('tallas', 'perPage'));
    }

    public function create()
    {
        $optionsTipoTalla = [
            'CALZADO' => 'Calzado',
            'ROPA' => 'Ropa',
            'UNICA' => 'Única',
        ];

        return view('talla.create', compact('optionsTipoTalla'));
    }

    public function store(StoreTallaRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                Talla::create([
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'tipo_talla' => $data['tipo_talla'],
                    'orden' => $data['orden'] ?? 0,
                    'estado' => $data['estado'] ?? 1,
                ]);
            });

            return redirect()
                ->route('tallas.index')
                ->with('success', 'Talla registrada correctamente');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al registrar la talla: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Talla $talla)
    {
        $optionsTipoTalla = [
            'CALZADO' => 'Calzado',
            'ROPA' => 'Ropa',
            'UNICA' => 'Única',
        ];

        return view('talla.edit', compact('talla', 'optionsTipoTalla'));
    }

    public function update(UpdateTallaRequest $request, Talla $talla)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $talla) {
                $talla->update([
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'tipo_talla' => $data['tipo_talla'],
                    'orden' => $data['orden'] ?? 0,
                    'estado' => $data['estado'] ?? 1,
                ]);
            });

            return redirect()
                ->route('tallas.index')
                ->with('success', 'Talla editada correctamente');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al editar la talla: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $talla = Talla::withTrashed()->findOrFail($id);

            if ($talla->trashed()) {
                $talla->restore();
                $message = 'Talla restaurada correctamente';
            } else {
                $talla->delete();
                $message = 'Talla eliminada correctamente';
            }

            return redirect()
                ->route('tallas.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la talla: ' . $e->getMessage()]);
        }
    }
}