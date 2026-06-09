<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaOperacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuditoriaOperacionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver_auditoria', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = AuditoriaOperacion::with('user')
            ->latest('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('entidad')) {
            $query->where('entidad', $request->entidad);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $auditorias = $query->get();
        $usuarios = User::orderBy('name')->get();

        $entidades = AuditoriaOperacion::query()
            ->select('entidad')
            ->distinct()
            ->orderBy('entidad')
            ->pluck('entidad');

        $acciones = AuditoriaOperacion::query()
            ->select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        return view('auditoria_operacion.index', compact('auditorias', 'usuarios', 'entidades', 'acciones'));
    }

    public function show(AuditoriaOperacion $auditoriaOperacion)
    {
        $auditoriaOperacion->load('user');

        return view('auditoria_operacion.show', compact('auditoriaOperacion'));
    }
}