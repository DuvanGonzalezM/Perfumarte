<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transformation extends Model
{
    use HasFactory;

    protected $primaryKey = 'transformation_id';

    protected $fillable = [
        'inventory_id',
        'escence',
        'dipropylene',
        'solvent',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function Inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class,'inventory_id');
    }
}
