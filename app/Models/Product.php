<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'reference',
        'name',
        'measurement_unit',
        'supplier_id',
        'commercial_reference',
        'category',
        'operational_role',
        'dependents',
        'code',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id');
    }

    public function productEntry(): HasMany
    {
        return $this->hasMany(ProductEntry::class, 'product_id');
    }

    /**
     * Producto que cumple un rol único en la operación (bolsa de regalo,
     * disolvente, dipropileno). Devuelve null si nadie lo tiene asignado, que
     * es lo que ocurre en una base recién cargada: el módulo que lo necesite
     * debe avisarlo, no fallar con un error de base de datos.
     */
    public static function findByRole(string $role): ?self
    {
        return static::where('operational_role', $role)->where('status', 1)->first();
    }

    public function scopeOperationalRole($query, string|array $roles)
    {
        return $query->whereIn('operational_role', (array) $roles);
    }

    /**
     * Insumos que se mezclan con las esencias. Varios módulos los excluyen del
     * listado de referencias porque no se venden ni se despachan sueltos.
     */
    public static function rawMaterialIds(): array
    {
        return static::operationalRole(['dipropylene', 'solvent'])->pluck('product_id')->all();
    }
}
