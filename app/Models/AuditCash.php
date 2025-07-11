<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditCash extends Model
{
    use HasFactory;
    protected $table = 'audit_cash'; 

    protected $primaryKey = 'audit_cash_id'; 

    protected $fillable = [
        'id_audits', 
        'money_in_box',
        'money_in_digital',
        'confirmation_cash',
        'confirmation_digital',
        'observation',
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class, 'id_audits');
    }
}
