<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transformation extends Model
{
    use HasFactory;

    protected $primaryKey = 'transformation_id ';

    protected $fillable = [
        'inventory_id',
        'quantity',
    ];

    public function Inventory(): HasMany
    {
        return $this->hasMany(Inventory:: class);
    }
}
