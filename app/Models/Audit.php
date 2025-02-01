<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_audits'; // Asegúrate de que esto coincida con tu migración

    protected $fillable = [
        'user_id', // Relación con la tabla users
        'type_audit', // Tipo de auditoría
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditInventories()
    {
        return $this->hasMany(AuditInventory::class, 'id_audits');
    }

    public function auditCashes()
    {
        return $this->hasOne(AuditCash::class, 'id_audits');
    }
}