<?php

namespace App\Http\Requests;

use App\Enums\AmbienteSistema;
use App\Enums\TipoComprobante;
use App\Enums\UsoComprobante;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_comprobantes') ?? false;
    }

    public function rules(): array
    {
        $comprobante = $this->route('comprobante');

        return [
            'tipo_comprobante' => ['required', Rule::enum(TipoComprobante::class)],
            'serie' => [
                'required',
                'string',
                'max:20',
                Rule::unique('comprobantes', 'serie')
                    ->ignore($comprobante?->id)
                    ->where(function ($query) {
                        return $query
                            ->where('tipo_comprobante', $this->input('tipo_comprobante'))
                            ->where('uso_comprobante', $this->input('uso_comprobante'));
                    }),
            ],
            'uso_comprobante' => ['required', Rule::enum(UsoComprobante::class)],
            'correlativo_actual' => ['required', 'integer', 'min:0'],
            'es_electronico' => ['nullable', 'boolean'],
            'ambiente' => ['required', Rule::enum(AmbienteSistema::class)],
            'estado' => ['required', 'boolean'],
        ];
    }
}