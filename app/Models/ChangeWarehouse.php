<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeWarehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'change_warehouse_id';
    protected $fillable = [
        'inventory_id',
        'quantity',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
   
    public function invetory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class,foreignKey: 'inventory_id');
    }
}
