<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuickClienteRequest;
use App\Models\Cliente;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteQuickController extends Controller
{
    public function store(StoreQuickClienteRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $cliente = DB::transaction(function () use ($request) {
                $data = $request->validated();

                $persona = Persona::withTrashed()
                    ->where('documento_id', $data['documento_id'])
                    ->where('numero_documento', $data['numero_documento'])
                    ->first();

                $personaData = [
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
                ];

                if ($persona) {
                    if ($persona->trashed()) {
                        $persona->restore();
                    }
                    $persona->update($personaData);
                } else {
                    $persona = Persona::create($personaData);
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

            $payload = [
                'id' => $cliente->id,
                'label' => $persona?->razon_social
                    ?? trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? '')),
                'tipo_persona' => $persona?->tipo_persona,
                'documento' => $persona?->documento?->codigo,
                'numero_documento' => $persona?->numero_documento,
            ];

            // Si la petición viene de AJAX (el modal)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Cliente registrado correctamente.',
                    'cliente' => $payload, // Enviamos el payload dentro de 'cliente'
                ], 201);
            }

            return back()->with('success', 'Cliente registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar cliente rápido', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payload' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'No se pudo registrar el cliente.',
                    'error' => $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Error al registrar el cliente: ' . $e->getMessage()])
                ->withInput();
        }
    }
}