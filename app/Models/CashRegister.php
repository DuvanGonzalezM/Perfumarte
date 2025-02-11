<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Sale;
use App\Models\Location;

class CashRegister extends Model
{
    use HasFactory;

    protected $primaryKey = 'cash_register_id';

    protected $fillable = [
        'location_id',
        'total_collected',
        'count_100_bill',
        'count_50_bill',
        'count_20_bill',
        'count_10_bill',
        'count_5_bill',
        'count_2_bill',
        'total_coins',
        'observations',
        'confirmationclosingcash',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cash_register_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
