<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'type_report',
        'start_date_report',
        'end_date_report',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
