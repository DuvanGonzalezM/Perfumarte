<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DamageReturn extends Model
{
    use HasFactory;

    protected $primaryKey = 'damage_return_id';

    protected $fillable = [
        'status',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function damageReturnDetail(): HasMany
    {
        return $this->hasMany(DamageReturnDetail::class,'damage_return_id');
    }
}
