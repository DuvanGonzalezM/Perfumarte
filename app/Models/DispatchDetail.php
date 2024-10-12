<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchDetail extends Model
{
    use HasFactory;
    protected $table = 'dispatches_detail';

    protected $primaryKey = 'dispatchs_detail_id';

    protected $fillable = [
        'dispatch_id',
        'inventory_id',
        'warehouse_id',
        'dispatched_quantity',
        'observations'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
