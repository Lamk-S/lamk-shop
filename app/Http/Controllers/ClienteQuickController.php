<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRapidoRequest;
use App\Models\Cliente;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClienteQuickController extends Controller
{
    public function store(StoreClienteRapidoRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cliente = DB::transaction(function () use ($data) {
            $persona = Persona::updateOrCreate(
                [
                    'documento_id' => $data['documento_id'],
                    'numero_documento' => $data['numero_documento'],
                ],
                [
                    'tipo_persona' => $data['tipo_persona'],
                    'nombres' => $data['nombres'] ?? null,
                    'apellidos' => $data['apellidos'] ?? null,
                    'razon_social' => $data['razon_social'] ?? null,
                    'direccion' => $data['direccion'] ?? null,
                    'telefono' => $data['telefono'] ?? null,
                    'email' => $data['email'] ?? null,
                    'estado' => 1,
                ]
            );

            $cliente = Cliente::updateOrCreate(
                ['persona_id' => $persona->id],
                ['estado' => 1]
            );

            return $cliente->load('persona.documento');
        });

        return response()->json([
            'message' => 'Cliente registrado correctamente.',
            'data' => [
                'id' => $cliente->id,
                'persona_id' => $cliente->persona_id,
                'text' => $cliente->persona?->nombre_completo . ' - ' . $cliente->persona?->documento?->codigo . ' ' . $cliente->persona?->numero_documento,
            ],
        ]);
    }
}