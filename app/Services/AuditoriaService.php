<?php

namespace App\Services;

use App\Models\AuditoriaOperacion;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaService
{
    public function registrar(
        string $entidad,
        ?int $entidadId,
        string $accion,
        array $antes = [],
        array $despues = [],
        ?User $user = null,
        ?Request $request = null
    ): AuditoriaOperacion {
        return AuditoriaOperacion::create([
            'user_id' => $user?->id,
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'accion' => $accion,
            'antes' => !empty($antes) ? $antes : null,
            'despues' => !empty($despues) ? $despues : null,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metodo_http' => $request?->method(),
            'ruta' => $request?->fullUrl(),
        ]);
    }
}