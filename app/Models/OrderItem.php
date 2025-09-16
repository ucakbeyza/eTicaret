<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable= [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getLineTotalAttribute()
    {
        $lineTotal = $this->quantity * $this->product_price_snapshot;
        return round($lineTotal, 2);
    }

}
