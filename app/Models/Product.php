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
        'measurement_unit',
        'supplier_id',
        'commercial_reference',
        'category',
    ];
    protected $casts = [
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
}
