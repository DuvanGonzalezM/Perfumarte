<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CashClosure;
use App\Models\User;
use App\Models\SaleDetail;

class Sale extends Model
{
    use HasFactory;

    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'cash_closure_id',
        'total',
        'payment_method',
        'transaction_code',
        'user_id',
        'type',
    ];

    public function cashClosure()
    {
        return $this->belongsTo(CashClosure::class, 'cash_closure_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }
}
