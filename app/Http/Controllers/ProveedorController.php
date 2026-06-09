<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Documento;
use App\Models\Persona;
use App\Models\Proveedor;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_proveedores', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $proveedores = Proveedor::with('persona.documento')->withTrashed()->latest('id')->get();

        return view('proveedor.index', compact('proveedores'));
    }

    public function create()
    {
        $documentos = Documento::where('estado', 1)->get();

        $optionsTipoPersona = [
            'natural' => 'Natural',
            'juridica' => 'Jurídica',
        ];

        return view('proveedor.create', compact('documentos', 'optionsTipoPersona'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $persona = Persona::create($request->validated());

                $persona->proveedor()->create([
                    'estado' => 1,
                ]);
            });

            return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el proveedor: ' . $e->getMessage()]);
        }
    }

    public function edit(Proveedor $proveedor)
    {
        $proveedor->load('persona.documento');
        $documentos = Documento::where('estado', 1)->get();

        $optionsTipoPersona = [
            'natural' => 'Natural',
            'juridica' => 'Jurídica',
        ];

        return view('proveedor.edit', compact('proveedor', 'documentos', 'optionsTipoPersona'));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        try {
            DB::transaction(function () use ($request, $proveedor) {
                $proveedor->persona->update($request->validated());
            });

            return redirect()->route('proveedores.index')->with('success', 'Proveedor editado correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el proveedor: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $proveedor = Proveedor::withTrashed()->findOrFail($id);
            $persona = Persona::withTrashed()->findOrFail($proveedor->persona_id);

            if ($proveedor->trashed()) {
                $persona->restore();
                $proveedor->restore();
                $message = 'Proveedor restaurado correctamente';
            } else {
                $proveedor->delete();
                $persona->delete();
                $message = 'Proveedor eliminado correctamente';
            }

            return redirect()->route('proveedores.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el proveedor: ' . $e->getMessage()]);
        }
    }
}