<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;


    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
    ];

    public function changeWarehouse(): HasMany
    {
        return $this->hasMany(ChangeWarehouse::class);
    }

    public function warehouse(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function requestDetail(): BelongsTo
    {
        return $this->belongsTo(RequestDetail::class);
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function trasnformation(): BelongsTo
    {
        return $this->belongsTo(Transformation::class);
    }

    public function detailDispatch(): BelongsTo
    {
        return $this->belongsTo(DispatchDetail::class);
    }
}

