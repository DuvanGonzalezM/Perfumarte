<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispatch extends Model
{
    use HasFactory;

    protected $primaryKey = 'dispatch_id';

    protected $fillable = [
        'status',
    ];
    protected $casts = [
        'created_at' => 'datatime',
        'updated_at' => 'datatime'
    ];

    public function dispatchDetail(): BelongsTo
    {
        return $this->belongsTo(DispatchDetail::class);
    }
}
