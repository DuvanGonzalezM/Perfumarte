<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestPrais extends Model
{
    use HasFactory;
    protected $table = 'requests';

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_type',
        'user_id',
        'location_id',
        'status',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function detailRequest(): HasMany
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }
}
