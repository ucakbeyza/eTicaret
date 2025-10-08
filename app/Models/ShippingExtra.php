<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingExtra extends Model
{
    protected $fillable = [
        'shipping_company_id',
        'city_id',
        'extra_price'
    ];

    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
