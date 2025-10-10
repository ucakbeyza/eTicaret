<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'product_id',
        'type'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
