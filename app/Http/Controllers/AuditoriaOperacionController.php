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
        $query = AuditoriaOperacion::with('user:id,name')->latest('id');

        $query->when($request->filled('usuario_id'), fn($q) => $q->where('user_id', $request->usuario_id))
              ->when($request->filled('modulo'), fn($q) => $q->where('entidad', $request->modulo))
              ->when($request->filled('accion'), fn($q) => $q->where('accion', $request->accion))
              ->when($request->filled('fecha'), fn($q) => $q->whereDate('created_at', $request->fecha));

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $auditorias = $query->paginate($perPage)->withQueryString();
        
        $usuarios = User::orderBy('name')->get(['id', 'name']);
        
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
            'acciones',
            'perPage'
        ));
    }

    public function show(AuditoriaOperacion $auditoriaOperacion)
    {
        $auditoria = $auditoriaOperacion->load('user');
        return view('auditoria_operacion.show', compact('auditoria'));
    }
}