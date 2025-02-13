<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_audits'; 

    protected $fillable = [
        'user_id',
        'type_audit', 
        'location_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditInventory()
    {
        return $this->hasMany(AuditInventory::class, 'id_audits');
    }

    public function auditCashes()
    {
        return $this->hasMany(AuditCash::class, 'id_audits');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
