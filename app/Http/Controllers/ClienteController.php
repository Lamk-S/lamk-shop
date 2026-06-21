<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_clientes', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Cliente::with([
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

        $clientes = $query->paginate($perPage)->withQueryString();

        return view('cliente.index', compact('clientes', 'perPage'));
    }

    public function create()
    {
        $documentos = Documento::where('estado', 1)->orderBy('codigo')->get();
        $optionsTipoPersona = ['natural' => 'Natural', 'juridica' => 'Jurídica'];

        return view('cliente.create', compact('documentos', 'optionsTipoPersona'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $persona = Persona::create($request->validated());
                $persona->cliente()->create(['estado' => 1]);
            });

            return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el cliente: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load([
            'persona' => fn ($q) => $q->withTrashed(),
            'persona.documento',
        ]);

        $documentos = Documento::where('estado', 1)->orderBy('codigo')->get();
        $optionsTipoPersona = ['natural' => 'Natural', 'juridica' => 'Jurídica'];

        return view('cliente.edit', compact('cliente', 'documentos', 'optionsTipoPersona'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        try {
            DB::transaction(function () use ($request, $cliente) {
                $cliente->persona->update($request->validated());
                $cliente->update(['estado' => $request->boolean('estado', true)]);
            });

            return redirect()->route('clientes.index')->with('success', 'Cliente editado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el cliente: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Cliente $cliente)
    {
        try {
            $persona = Persona::withTrashed()->findOrFail($cliente->persona_id);

            if ($cliente->trashed()) {
                $persona->restore();
                $cliente->restore();
                $message = 'Cliente restaurado correctamente';
            } else {
                $cliente->delete();
                $persona->delete();
                $message = 'Cliente eliminado correctamente';
            }

            return redirect()->route('clientes.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el cliente: ' . $e->getMessage()]);
        }
    }
}