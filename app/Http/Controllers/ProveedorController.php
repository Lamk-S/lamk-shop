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
            new Middleware('permission:ver-proveedor|crear-proveedor|editar-proveedor|eliminar-proveedor', only: ['index']),
            new Middleware('permission:crear-proveedor', only: ['create', 'store']),
            new Middleware('permission:editar-proveedor', only: ['edit', 'update']),
            new Middleware('permission:eliminar-proveedor', only: ['destroy']),
        ];
    }

    public function index()
    {
        $proveedores = Proveedor::with('persona.documento')->latest()->get();
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
                $persona->proveedor()->create();
            });

            return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el proveedor: ' . $e->getMessage()]);
        }
    }

    public function edit(Proveedor $proveedor)
    {
        $proveedor->load('persona.documento');
        $documentos = Documento::where('estado', 1)->get();

        return view('proveedor.edit', compact('proveedor', 'documentos'));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        try {
            DB::transaction(function () use ($request, $proveedor) {
                $proveedor->persona->update($request->validated());
            });

            return redirect()->route('proveedores.index')->with('success', 'Proveedor editado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el proveedor: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $proveedor = Proveedor::with('persona')->findOrFail($id);

            if ($proveedor->persona->deleted_at) {
                $proveedor->persona->restore();
                $proveedor->restore();
                $message = 'Proveedor restaurado';
            } else {
                $proveedor->delete();
                $proveedor->persona->delete();
                $message = 'Proveedor eliminado';
            }

            return redirect()->route('proveedores.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el proveedor: ' . $e->getMessage()]);
        }
    }
}