<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;

use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function showInventoryAudit()
    {
        return Inertia::render('Audit/AuditInventory'); // Vista para auditoría de inventario
    }

    public function showCashAudit()
    {
        return Inertia::render('Audit/AuditCash'); // Vista para auditoría de caja
    }
}
