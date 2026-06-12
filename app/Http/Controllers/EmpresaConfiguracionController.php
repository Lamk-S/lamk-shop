<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmpresaConfiguracionRequest;
use App\Models\EmpresaConfiguracion;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class EmpresaConfiguracionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_configuracion', only: ['index', 'show', 'edit', 'update']),
        ];
    }

    public function index()
    {
        $empresaConfiguracion = EmpresaConfiguracion::first();

        if (!$empresaConfiguracion) {
            return redirect()
                ->route('panel')
                ->with('warning', 'No existe configuración de empresa.');
        }

        return redirect()->route('empresa-configuracion.show', $empresaConfiguracion);
    }

    public function show(EmpresaConfiguracion $empresaConfiguracion)
    {
        return view('empresa_configuracion.show', compact('empresaConfiguracion'));
    }

    public function edit(EmpresaConfiguracion $empresaConfiguracion)
    {
        return view('empresa_configuracion.edit', compact('empresaConfiguracion'));
    }

    public function update(UpdateEmpresaConfiguracionRequest $request, EmpresaConfiguracion $empresaConfiguracion)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                if (
                    $empresaConfiguracion->logo_path &&
                    Storage::disk('public')->exists($empresaConfiguracion->logo_path)
                ) {
                    Storage::disk('public')->delete($empresaConfiguracion->logo_path);
                }

                $logoName = time() . '_' . $request->file('logo')->getClientOriginalName();
                $request->file('logo')->storeAs('empresa', $logoName, 'public');
                $data['logo_path'] = 'empresa/' . $logoName;
            }

            $empresaConfiguracion->update($data);

            return redirect()
                ->route('empresa-configuracion.show', $empresaConfiguracion)
                ->with('success', 'Configuración de empresa actualizada correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar la configuración: ' . $e->getMessage()])
                ->withInput();
        }
    }
}