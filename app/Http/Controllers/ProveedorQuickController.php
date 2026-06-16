<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuickProveedorRequest;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProveedorQuickController extends Controller
{
    public function store(StoreQuickProveedorRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $proveedor = DB::transaction(function () use ($request) {
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

                $proveedor = Proveedor::withTrashed()
                    ->where('persona_id', $persona->id)
                    ->first();

                if ($proveedor) {
                    if ($proveedor->trashed()) {
                        $proveedor->restore();
                    }
                    $proveedor->update(['estado' => 1]);
                } else {
                    $proveedor = Proveedor::create([
                        'persona_id' => $persona->id,
                        'estado' => 1,
                    ]);
                }

                return $proveedor->load('persona.documento');
            });

            $persona = $proveedor->persona;
            $label = $persona?->razon_social
                ?? trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? ''));

            $payload = [
                'id' => $proveedor->id,
                'label' => $label,
                'tipo_persona' => $persona?->tipo_persona,
                'documento' => $persona?->documento?->codigo,
                'numero_documento' => $persona?->numero_documento,
            ];

            // Si la petición viene de AJAX (el modal)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Proveedor registrado correctamente.',
                    'proveedor' => $payload, // Enviamos el payload dentro de 'proveedor'
                ], 201);
            }

            return back()->with('success', 'Proveedor registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar proveedor rápido', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payload' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'No se pudo registrar el proveedor.',
                    'error' => $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Error al registrar el proveedor: ' . $e->getMessage()])
                ->withInput();
        }
    }
}