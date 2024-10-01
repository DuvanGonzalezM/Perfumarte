<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->belongsTo(RequestPrais::class, foreignKey:'request_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class,foreignKey:'inventory_id');
    }
}
