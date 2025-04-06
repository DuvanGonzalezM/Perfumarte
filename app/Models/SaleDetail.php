<?php

namespace App\Models;

use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'sale_detail_id';

    protected $fillable = [
        'inventory_id',
        'sale_id',
        'quantity',
        'units',
        'drops',
        'price',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
