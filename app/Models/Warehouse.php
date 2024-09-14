<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'warehouse_id';

    protected $fillable = [
        'location_id',
        'name',
    ];
    protected $casts = [
        'created_at' => 'datatime',
        'updated_at' => 'datatime'
    ];
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
