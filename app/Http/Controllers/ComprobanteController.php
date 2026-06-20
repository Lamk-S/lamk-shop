<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComprobanteRequest;
use App\Http\Requests\UpdateComprobanteRequest;
use App\Models\Comprobante;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ComprobanteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_comprobantes', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Comprobante::query()
            ->withTrashed()
            ->latest('id');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('tipo_comprobante', 'like', "%{$search}%")
                    ->orWhere('serie', 'like', "%{$search}%")
                    ->orWhere('uso_comprobante', 'like', "%{$search}%")
                    ->orWhere('ambiente', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_comprobante')) {
            $query->where('tipo_comprobante', $request->input('tipo_comprobante'));
        }

        if ($request->filled('uso_comprobante')) {
            $query->where('uso_comprobante', $request->input('uso_comprobante'));
        }

        if ($request->filled('estado')) {
            match ($request->input('estado')) {
                'activo' => $query->where('estado', 1)->whereNull('deleted_at'),
                'inactivo' => $query->where('estado', 0)->whereNull('deleted_at'),
                'eliminado' => $query->onlyTrashed(),
                default => null,
            };
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $comprobantes = $query->paginate($perPage)->withQueryString();

        return view('comprobante.index', compact('comprobantes', 'perPage'));
    }

    public function show(Comprobante $comprobante)
    {
        return view('comprobante.show', compact('comprobante'));
    }

    public function edit(Comprobante $comprobante)
    {
        $optionsTipoComprobante = [
            'TICKET' => 'Ticket',
            'BOLETA' => 'Boleta',
            'FACTURA' => 'Factura',
            'NOTA_CREDITO' => 'Nota de crédito',
            'NOTA_DEBITO' => 'Nota de débito',
        ];

        $optionsUsoComprobante = [
            'VENTA' => 'Venta',
            'COMPRA' => 'Compra',
        ];

        $optionsAmbiente = [
            'SIMULADO' => 'Simulado',
            'PRODUCCION' => 'Producción',
        ];

        return view('comprobante.edit', compact(
            'comprobante',
            'optionsTipoComprobante',
            'optionsUsoComprobante',
            'optionsAmbiente'
        ));
    }

    public function update(UpdateComprobanteRequest $request, Comprobante $comprobante)
    {
        try {
            DB::transaction(function () use ($request, $comprobante) {
                if ($comprobante->trashed()) {
                    $comprobante->restore();
                }

                $comprobante->update($request->validated());
            });

            return redirect()
                ->route('comprobantes.index')
                ->with('success', 'Comprobante actualizado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar el comprobante: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Comprobante $comprobante)
    {
        try {
            DB::transaction(function () use ($comprobante) {
                if ($comprobante->trashed()) {
                    $comprobante->restore();
                    $comprobante->update(['estado' => 1]); 
                } else {
                    $comprobante->delete();
                    $comprobante->update(['estado' => 0]);
                }
            });

            $message = $comprobante->trashed()
                ? 'Comprobante desactivado y enviado a la papelera correctamente'
                : 'Comprobante restaurado y activado correctamente';

            return redirect()
                ->route('comprobantes.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al modificar el comprobante: ' . $e->getMessage()]);
        }
    }
}