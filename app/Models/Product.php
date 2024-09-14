<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'reference',
        'measurement_unit',
        'supplier_id',
    ];
    protected $casts = [
        'created_at' => 'datatime',
        'updated_at' => 'datatime'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryProduct(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function productEntry(): BelongsTo
    {
        return $this->belongsTo(ProductEntry::class);
    }
}
