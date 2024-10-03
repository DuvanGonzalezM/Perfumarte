<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestDetail extends Model
{
    use HasFactory;

    protected $table = 'requests_detail';
    protected $primaryKey = 'request_detail_id';

    protected $fillable = [
        'request_id',
        'inventory_id',
        'quantity',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function Request(): BelongsTo
    {
        return $this->belongsTo(RequestPrais::class,  'request_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
