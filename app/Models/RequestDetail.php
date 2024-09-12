<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Request;

class RequestDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'request_detail_id';

    protected $fillable = [
        'request_id',
        'inventory_id',
        'quantity',
    ]

    ;

    public function Request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function inventoryRequestDetail(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

}
