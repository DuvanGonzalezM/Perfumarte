<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'name',
        'address',
        'zone_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function userLocation(): HasMany
    {
        return $this->hasMany(User::class,'location_id');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class,'location_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class,foreignKey: 'zone_id');
    }
}
