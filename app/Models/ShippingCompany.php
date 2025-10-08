<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    protected $fillable = [
        'name',
        'base_price'
    ];

    public function shippingExtras()
    {
        return $this->hasMany(ShippingExtra::class);
    }
    
}
