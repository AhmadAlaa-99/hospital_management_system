<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name', 'generic_name', 'quantity', 'unit_price', 'expiry_date', 'min_stock_level', 'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'unit_price' => 'decimal:2',
    ];

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock_level;
    }

    public function dispensings() { return $this->hasMany(PharmacyDispensing::class); }
}
