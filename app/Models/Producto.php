<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table    = 'producto';
    protected $fillable = [
        'proveedor_id',
        'nombre', 'descripcion', 'precio', 'unidad',
        'stockMinimo', 'categoria',
        'codigoBarras', 'diasPerecederoMax', 'imagen', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'precio' => 'decimal:2',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'producto_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    // ── Stock calculado desde movimientos ─────────────────────

    /**
     * Stock disponible = SUM(entradas) - SUM(salidas) en movimientos.
     * Usa la suma de cantidad_disponible de lotes activos para mayor eficiencia.
     */
    public function getStockCalculadoAttribute(): int
    {
        return (int) $this->lotes()->sum('cantidad_disponible');
    }

    /**
     * Lote más próximo a vencer con stock disponible.
     */
    public function getLoteProximoVencerAttribute(): ?Lote
    {
        return $this->lotes()
            ->where('cantidad_disponible', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->orderBy('fecha_vencimiento')
            ->first();
    }

    // ── Estado de stock ───────────────────────────────────────

    public function getStockEstadoAttribute(): string
    {
        $stock = $this->stock_calculado;
        if ($stock <= 0) return 'agotado';
        if ($this->stockMinimo !== null && $stock <= $this->stockMinimo) return 'bajo';
        return 'ok';
    }
}
