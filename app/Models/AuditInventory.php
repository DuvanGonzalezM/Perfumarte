<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditInventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_audit_inventory'; // Asegúrate de que esto coincida con tu migración

    protected $fillable = [
        'id_audits', // Relación con la tabla audits
        'inventory_id', // Relación con la tabla products
        'confirmation_inventory',
        'quantity_system',
        'observation',
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class, 'id_audits');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}