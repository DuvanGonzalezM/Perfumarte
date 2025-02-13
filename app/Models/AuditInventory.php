<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditInventory extends Model
{
    use HasFactory;
    protected $table = 'audit_inventory';
    protected $primaryKey = 'id_audit_inventory'; 
    protected $fillable = [
        'id_audits',
        'inventory_id', 
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
