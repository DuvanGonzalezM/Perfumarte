<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispatchDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'dispatchs_detail_id';

    protected $fillable = [
        'dispatch_id',
        'inventory_id',
        'dispatched_quantity',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function dispatch(): HasMany
    {
        return $this->hasMany(Dispatch::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
