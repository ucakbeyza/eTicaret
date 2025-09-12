<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;

class Cart extends Model
{   
    protected $appends = ['subtotal'];
    protected $fillable = [
        'user_id',
        'product_id', 
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //dikkat
    public function getSubtotalAttribute()
    {

        $subtotal = $this->product ? $this->product->price * $this->quantity : 0;
        return round($subtotal, 2); 
    }


}
