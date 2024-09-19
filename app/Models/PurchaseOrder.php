<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_order_id';
    protected $fillable = [
        'supplier_order',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function productEntryOrder(): HasMany
    {
        return $this->hasMany(ProductEntry::class, 'purchase_order_id');
    }
}
