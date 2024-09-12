<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_type',
        'user_id',
        'status',
    ];

    public function userRequest(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailRequest(): HasMany
    {
        return $this->hasMany(RequestDetail::class);
    }
}
