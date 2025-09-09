<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReturnDetail extends Model
{
    use HasFactory;
    protected $table = 'damage_return_detail';

    protected $primaryKey = 'damage_return_detail_id';

    protected $fillable = [
        'damage_return_id',
        'inventory_id',
        'warehouse_id',
        'damage_quantity',
        'observations'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function damageReturn(): BelongsTo
    {
        return $this->belongsTo(DamageReturn::class, 'damage_return_id');
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
