<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductEntry extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_entry_id';

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orderpurchase(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
