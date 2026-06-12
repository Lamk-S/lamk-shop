<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
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

    public function index()
    {
        $clientes = Cliente::with('persona.documento')->withTrashed()->latest('id')->get();

        return view('cliente.index', compact('clientes'));
    }

    public function create()
    {
        $documentos = Documento::where('estado', 1)->get();
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
        $cliente->load('persona.documento');
        $documentos = Documento::where('estado', 1)->get();
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