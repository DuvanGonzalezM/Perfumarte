<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'request_detail_id';

    protected $fillable = [
        'request_id',
        'inventory_id',
        'quantity',
    ]

    ;
}
