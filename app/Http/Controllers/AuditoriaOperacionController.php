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
        $query = AuditoriaOperacion::with('user')->latest('id');

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        if ($request->filled('modulo')) {
            $query->where('entidad', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $auditorias = $query->paginate(50)->withQueryString();

        $usuarios = User::orderBy('name')->get();
        $modulos = AuditoriaOperacion::query()
            ->whereNotNull('entidad')
            ->select('entidad')
            ->distinct()
            ->orderBy('entidad')
            ->pluck('entidad');

        $acciones = AuditoriaOperacion::query()
            ->whereNotNull('accion')
            ->select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        return view('auditoria_operacion.index', compact(
            'auditorias',
            'usuarios',
            'modulos',
            'acciones'
        ));
    }

    public function show(AuditoriaOperacion $auditoriaOperacione)
    {
        $auditoria = $auditoriaOperacione->load('user');

        return view('auditoria_operacion.show', compact('auditoria'));
    }
}