<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-cliente|crear-cliente|editar-cliente|eliminar-cliente', only: ['index']),
            new Middleware('permission:crear-cliente', only: ['create', 'store']),
            new Middleware('permission:editar-cliente', only: ['edit', 'update']),
            new Middleware('permission:eliminar-cliente', only: ['destroy']),
        ];
    }

    public function index()
    {
        $clientes = Cliente::with('persona.documento')->latest()->get();
        return view('cliente.index', compact('clientes'));
    }

    public function create()
    {
        $documentos = Documento::where('estado', 1)->get();
        $optionsTipoPersona = [
            'natural' => 'Natural',
            'juridica' => 'Jurídica',
        ];

        return view('cliente.create', compact('documentos', 'optionsTipoPersona'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $persona = Persona::create($request->validated());
                $persona->cliente()->create();
            });

            return redirect()->route('clientes.index')->with('success', 'Cliente registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el cliente: ' . $e->getMessage()]);
        }
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load('persona.documento');
        $documentos = Documento::where('estado', 1)->get();

        return view('cliente.edit', compact('cliente', 'documentos'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        try {
            DB::transaction(function () use ($request, $cliente) {
                $cliente->persona->update($request->validated());
            });

            return redirect()->route('clientes.index')->with('success', 'Cliente editado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el cliente: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $cliente = Cliente::with('persona')->findOrFail($id);

            if ($cliente->persona->deleted_at) {
                $cliente->persona->restore();
                $cliente->restore();
                $message = 'Cliente restaurado';
            } else {
                $cliente->delete();
                $cliente->persona->delete();
                $message = 'Cliente eliminado';
            }

            return redirect()->route('clientes.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el cliente: ' . $e->getMessage()]);
        }
    }
}