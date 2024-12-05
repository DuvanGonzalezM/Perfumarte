<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Sale;
use App\Models\Location;

class CashClosure extends Model
{
    use HasFactory;

    protected $primaryKey = 'cash_closure_id';

    protected $fillable = [
        'branch_id',
        'location_id',
        'total_collected',
        'total_products_sold',
        'count_100_bill',
        'count_50_bill',
        'count_20_bill',
        'count_10_bill',
        'count_5_bill',
        'count_2_bill',
        'count_1_bill',
        'total_coins',
        'observations',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cash_closure_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
