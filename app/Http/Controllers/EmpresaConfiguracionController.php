<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmpresaConfiguracionRequest;
use App\Models\EmpresaConfiguracion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class EmpresaConfiguracionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_configuracion', only: ['index', 'edit', 'update']),
        ];
    }

    public function index()
    {
        $configuracion = EmpresaConfiguracion::first();

        if (!$configuracion) {
            $configuracion = EmpresaConfiguracion::create([
                'razon_social' => 'Lamk Sports S.A.C.',
                'nombre_comercial' => 'Lamk Sports',
                'ruc' => '20123456789',
                'direccion_fiscal' => 'Av. Principal 123, Lima, Perú',
                'telefono' => null,
                'email' => null,
                'logo_path' => null,
                'moneda' => 'PEN',
                'igv_porcentaje' => 18.00,
                'modo_emision' => 'SIMULADA',
                'estado' => 1,
            ]);
        }

        return view('empresa_configuracion.edit', compact('configuracion'));
    }

    public function edit(EmpresaConfiguracion $empresaConfiguracion)
    {
        return view('empresa_configuracion.edit', [
            'configuracion' => $empresaConfiguracion,
        ]);
    }

    public function update(UpdateEmpresaConfiguracionRequest $request, EmpresaConfiguracion $empresaConfiguracion)
    {
        $data = $request->validated();

        try {
            if ($request->hasFile('logo_path')) {
                if ($empresaConfiguracion->logo_path && Storage::disk('public')->exists($empresaConfiguracion->logo_path)) {
                    Storage::disk('public')->delete($empresaConfiguracion->logo_path);
                }

                $logoName = time() . '_' . $request->file('logo_path')->getClientOriginalName();
                $request->file('logo_path')->storeAs('empresa', $logoName, 'public');
                $data['logo_path'] = 'empresa/' . $logoName;
            } else {
                $data['logo_path'] = $empresaConfiguracion->logo_path;
            }

            $empresaConfiguracion->update($data);

            return back()->with('success', 'Configuración de empresa actualizada correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar la configuración: ' . $e->getMessage(),
            ])->withInput();
        }
    }
}