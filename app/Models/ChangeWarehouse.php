<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeWarehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'change_warehouse_id';
    protected $fillable = [
        'inventory_id',
        'quantity',
    ];
   
}
