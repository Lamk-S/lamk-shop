<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTallaRequest;
use App\Http\Requests\UpdateTallaRequest;
use App\Models\Talla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TallaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_tallas', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $tallas = Talla::withTrashed()->latest('id')->get();

        return view('talla.index', compact('tallas'));
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
            Talla::create([
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'tipo_talla' => $data['tipo_talla'],
                'orden' => $data['orden'] ?? 0,
                'estado' => $data['estado'] ?? 1,
            ]);

            return redirect()->route('tallas.index')->with('success', 'Talla registrada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la talla: ' . $e->getMessage()]);
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
            $talla->update($data);

            return redirect()->route('tallas.index')->with('success', 'Talla editada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar la talla: ' . $e->getMessage()]);
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

            return redirect()->route('tallas.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la talla: ' . $e->getMessage()]);
        }
    }
}