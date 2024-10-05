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
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function changeWarehouse(): HasMany
    {
        return $this->hasMany(ChangeWarehouse::class,'inventory_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }

    public function requestDetail(): HasMany
    {
        return $this->hasMany(RequestDetail::class,'inventory_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function trasnformation(): BelongsTo
    {
        return $this->belongsTo(Transformation::class,'inventory_id');
    }

    public function detailDispatch(): HasMany
    {
        return $this->hasMany(DispatchDetail::class,'inventory_id');
    }
}
