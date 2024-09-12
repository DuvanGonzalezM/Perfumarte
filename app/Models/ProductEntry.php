<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductEntry extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_entry_id';

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
    ];
}
