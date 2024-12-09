<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryValidation extends Model
{
    use HasFactory;

    protected $primaryKey = 'inventory_validation_id';

    protected $fillable = [
        'user_id',
        'location_id',
        'date',
        'accepted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
