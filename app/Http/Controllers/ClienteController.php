<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\StoreQuickClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
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

        if ($request->filled('q')) {
            $search = trim($request->input('q'));

            $query->whereHas('persona', function ($q) use ($search) {
                $q->withTrashed()
                    ->where('numero_documento', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('razon_social', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_persona')) {
            $query->whereHas('persona', function ($q) use ($request) {
                $q->withTrashed()->where('tipo_persona', $request->input('tipo_persona'));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

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

    public function quickStore(StoreQuickClienteRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $cliente = DB::transaction(function () use ($request) {
                $data = $request->validated();

                $persona = Persona::withTrashed()
                    ->where('documento_id', $data['documento_id'])
                    ->where('numero_documento', $data['numero_documento'])
                    ->first();

                if ($persona) {
                    if ($persona->trashed()) {
                        $persona->restore();
                    }

                    $persona->update([
                        'tipo_persona' => $data['tipo_persona'],
                        'nombres' => $data['tipo_persona'] === 'natural' ? $data['nombres'] : null,
                        'apellidos' => $data['tipo_persona'] === 'natural' ? $data['apellidos'] : null,
                        'razon_social' => $data['tipo_persona'] === 'juridica' ? $data['razon_social'] : null,
                        'direccion' => $data['direccion'] ?? null,
                        'telefono' => $data['telefono'] ?? null,
                        'email' => $data['email'] ?? null,
                        'estado' => $data['estado'] ?? 1,
                    ]);
                } else {
                    $persona = Persona::create([
                        'tipo_persona' => $data['tipo_persona'],
                        'documento_id' => $data['documento_id'],
                        'numero_documento' => $data['numero_documento'],
                        'nombres' => $data['tipo_persona'] === 'natural' ? ($data['nombres'] ?? null) : null,
                        'apellidos' => $data['tipo_persona'] === 'natural' ? ($data['apellidos'] ?? null) : null,
                        'razon_social' => $data['tipo_persona'] === 'juridica' ? ($data['razon_social'] ?? null) : null,
                        'direccion' => $data['direccion'] ?? null,
                        'telefono' => $data['telefono'] ?? null,
                        'email' => $data['email'] ?? null,
                        'estado' => $data['estado'] ?? 1,
                    ]);
                }

                $cliente = Cliente::withTrashed()
                    ->where('persona_id', $persona->id)
                    ->first();

                if ($cliente) {
                    if ($cliente->trashed()) {
                        $cliente->restore();
                    }

                    $cliente->update(['estado' => 1]);
                } else {
                    $cliente = Cliente::create([
                        'persona_id' => $persona->id,
                        'estado' => 1,
                    ]);
                }

                return $cliente->load('persona.documento');
            });

            $persona = $cliente->persona;
            $label = $persona?->razon_social
                ?? trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? ''));

            $payload = [
                'id' => $cliente->id,
                'label' => $label,
                'tipo_persona' => $persona?->tipo_persona,
                'documento' => $persona?->documento?->codigo,
                'numero_documento' => $persona?->numero_documento,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cliente registrado correctamente.',
                    'cliente' => $payload,
                ], 201);
            }

            return back()->with('success', 'Cliente registrado correctamente.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No se pudo registrar el cliente.',
                ], 422);
            }

            return back()->withErrors(['error' => 'Error al registrar el cliente: ' . $e->getMessage()])->withInput();
        }
    }
}