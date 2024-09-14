<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use HasFactory;
    protected $primaryKey = 'rol_id';

    protected $fillable = [
        'name'
    ];
    protected $casts = [
        'created_at' => 'datatime',
        'updated_at' => 'datatime'
    ];

    public function user(): HasMany 
    {
        return $this->hasMany(User::class);
    }
}
