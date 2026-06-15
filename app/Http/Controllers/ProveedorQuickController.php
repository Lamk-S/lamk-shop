<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRapidoRequest;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProveedorQuickController extends Controller
{
    public function store(StoreProveedorRapidoRequest $request): JsonResponse
    {
        $data = $request->validated();

        $proveedor = DB::transaction(function () use ($data) {
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

            $proveedor = Proveedor::updateOrCreate(
                ['persona_id' => $persona->id],
                ['estado' => 1]
            );

            return $proveedor->load('persona.documento');
        });

        return response()->json([
            'message' => 'Proveedor registrado correctamente.',
            'data' => [
                'id' => $proveedor->id,
                'persona_id' => $proveedor->persona_id,
                'text' => $proveedor->persona?->nombre_completo . ' - ' . $proveedor->persona?->documento?->codigo . ' ' . $proveedor->persona?->numero_documento,
            ],
        ]);
    }
}