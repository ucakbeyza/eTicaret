<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_no',
        'total_price',
        'currency',
        'status',
        'paid_at',
    ];
    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
    ];
    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    } 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
