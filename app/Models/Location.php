<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'name',
        'address',
    ];

    public function userLocation(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function warehouse(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }
}
