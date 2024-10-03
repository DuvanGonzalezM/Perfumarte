<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispatch extends Model
{
    use HasFactory;

    protected $primaryKey = 'dispatch_id';

    protected $fillable = [
        'status',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function dispatchDetail(): HasMany
    {
        return $this->hasMany(DispatchDetail::class,'dispatch_id');
    }
}
