<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_order_id';
    protected $fillable = [
        'supplier_order',
    ];

    public function productEntryOrder(): BelongsTo
    {
        return $this->belongsTo(ProductEntry::class);
    }
}
