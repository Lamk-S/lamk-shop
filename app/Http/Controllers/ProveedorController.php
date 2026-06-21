<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Documento;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        $query = Proveedor::with([
                'persona' => fn ($q) => $q->withTrashed(),
                'persona.documento',
            ])
            ->withTrashed()
            ->latest('id');

        $query->when($request->filled('q'), function ($q) use ($request) {
            $search = trim((string) $request->input('q'));
            $q->whereHas('persona', function ($sub) use ($search) {
                $sub->withTrashed()
                    ->where('numero_documento', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('razon_social', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('tipo_persona'), fn($q) => $q->whereHas('persona', fn($sub) => $sub->withTrashed()->where('tipo_persona', $request->tipo_persona)))
        ->when($request->filled('estado'), fn($q) => $q->where('estado', $request->estado));

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $proveedores = $query->paginate($perPage)->withQueryString();

        return view('proveedor.index', compact('proveedores', 'perPage'));
    }

    public function create()
    {
        $documentos = Documento::where('estado', 1)->orderBy('codigo')->get();
        $optionsTipoPersona = ['natural' => 'Natural', 'juridica' => 'Jurídica'];

        return view('proveedor.create', compact('documentos', 'optionsTipoPersona'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $persona = Persona::create($request->validated());
                $persona->proveedor()->create(['estado' => 1]);
            });

            return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el proveedor: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Proveedor $proveedor)
    {
        $proveedor->load([
            'persona' => fn ($q) => $q->withTrashed(),
            'persona.documento',
        ]);

        $documentos = Documento::where('estado', 1)->orderBy('codigo')->get();
        $optionsTipoPersona = ['natural' => 'Natural', 'juridica' => 'Jurídica'];

        return view('proveedor.edit', compact('proveedor', 'documentos', 'optionsTipoPersona'));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        try {
            DB::transaction(function () use ($request, $proveedor) {
                $proveedor->persona->update($request->validated());
                $proveedor->update(['estado' => $request->boolean('estado', true)]);
            });

            return redirect()->route('proveedores.index')->with('success', 'Proveedor editado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el proveedor: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
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
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el proveedor: ' . $e->getMessage()]);
        }
    }
}