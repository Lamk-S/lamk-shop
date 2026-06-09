<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComprobanteRequest;
use App\Http\Requests\UpdateComprobanteRequest;
use App\Models\Comprobante;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ComprobanteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_comprobantes', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $comprobantes = Comprobante::withTrashed()
            ->latest('id')
            ->get();

        return view('comprobante.index', compact('comprobantes'));
    }

    public function create()
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

        return view('comprobante.create', compact(
            'optionsTipoComprobante',
            'optionsUsoComprobante',
            'optionsAmbiente'
        ));
    }

    public function store(StoreComprobanteRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                Comprobante::create([
                    'tipo_comprobante' => $data['tipo_comprobante'],
                    'serie' => $data['serie'],
                    'uso_comprobante' => $data['uso_comprobante'],
                    'correlativo_actual' => $data['correlativo_actual'] ?? 0,
                    'es_electronico' => $data['es_electronico'] ?? false,
                    'ambiente' => $data['ambiente'],
                    'estado' => $data['estado'] ?? 1,
                ]);
            });

            return redirect()
                ->route('comprobantes.index')
                ->with('success', 'Comprobante registrado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar el comprobante: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function edit(string $id)
    {
        $comprobante = Comprobante::withTrashed()->findOrFail($id);

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

    public function update(UpdateComprobanteRequest $request, string $id)
    {
        $comprobante = Comprobante::withTrashed()->findOrFail($id);

        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $comprobante) {
                if ($comprobante->trashed()) {
                    $comprobante->restore();
                }

                $comprobante->update([
                    'tipo_comprobante' => $data['tipo_comprobante'],
                    'serie' => $data['serie'],
                    'uso_comprobante' => $data['uso_comprobante'],
                    'correlativo_actual' => $data['correlativo_actual'],
                    'es_electronico' => $data['es_electronico'] ?? false,
                    'ambiente' => $data['ambiente'],
                    'estado' => $data['estado'],
                ]);
            });

            return redirect()
                ->route('comprobantes.index')
                ->with('success', 'Comprobante actualizado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar el comprobante: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $comprobante = Comprobante::withTrashed()->findOrFail($id);

            if ($comprobante->trashed()) {
                $comprobante->restore();
                $message = 'Comprobante restaurado correctamente';
            } else {
                $comprobante->delete();
                $message = 'Comprobante eliminado correctamente';
            }

            return redirect()
                ->route('comprobantes.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al modificar el comprobante: ' . $e->getMessage(),
            ]);
        }
    }
}