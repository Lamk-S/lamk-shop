<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'sesion_caja_id',
        'comprobante_id',
        'tipo_comprobante',
        'serie',
        'correlativo',
        'cliente_tipo_documento',
        'cliente_numero_documento',
        'cliente_nombre',
        'cliente_direccion',
        'cliente_email',
        'moneda',
        'fecha_emision',
        'subtotal',
        'descuento_total',
        'impuesto_total',
        'total',
        'monto_recibido',
        'vuelto_entregado',
        'estado_documento',
        'observacion',
        'motivo_anulacion',
        'anulado_at',
        'sunat_estado',
        'sunat_mensaje',
        'xml_path',
        'pdf_path',
        'qr_payload',
        'hash_resumen',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto_total' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto_entregado' => 'decimal:2',
        'anulado_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'comprobante_id');
    }

    public function detalles()
    {
        return $this->hasMany(ProductoVenta::class, 'venta_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'venta_id');
    }

    public function movimientosTesoreria()
    {
        return $this->hasMany(MovimientoTesoreria::class, 'venta_id');
    }
}