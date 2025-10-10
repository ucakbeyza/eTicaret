<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type', 
        'value',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'user_id',
        'category_id',
        'product_id',
        'min_order_amount',
        'max_discount_amount',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function isValid($userId = null)
    {
        $now = now();
        if (!$this->is_active) {
            return false;
        }
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        if ($this->usage_limit_per_user !== null && $userId !== null) {
            if ($this->userUsageCount($userId) >= $this->usage_limit_per_user) {
            return false;
        }
    }
        return true;
    }
    public function applyDiscount($total, $userId = null)
    {
        if (!$this->isValid($userId)) {
            return $total;
        }

        $discount = $this->value;
        
        if ($this->type === 'percentage') {
            $discount = $total * ($this->value / 100);
        }
        if ($this->min_order_amount !== null && $total < $this->min_order_amount) {
            return $total;
        }
        if ($this->max_discount_amount !== null) {
            $discount = min($discount, $this->max_discount_amount);
        }
        $discount = min($discount, $total);

        return $total - $discount;
    }
    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function decrementUsage()
    {
        if ($this->used_count > 0) {
            $this->decrement('used_count');
        }
    }
    public function userUsageCount($userId)
    {
        return CouponUsage::where('coupon_id', $this->id)
                          ->where('user_id', $userId)
                          ->value('usage_count') ?? 0;
    }
    public function incrementUserUsage($userId)
    {
        $usage = CouponUsage::firstOrCreate(
            ['coupon_id' => $this->id, 'user_id' => $userId],
            ['usage_count' => 0]
        );
        $usage->increment('usage_count');
    }
    public function decrementUserUsage($userId)
    {
        $usage = CouponUsage::where('coupon_id', $this->id)
                            ->where('user_id', $userId)
                            ->first();
        if ($usage && $usage->usage_count > 0) {
            $usage->decrement('usage_count');
            if ($usage->usage_count == 0) {
                $usage->delete();
            }
        }
    }
    
    private function generateUniqueCode($length = 8)
    {
        do {
            $code = strtoupper(bin2hex(random_bytes($length / 2)));
        } while (self::where('code', $code)->exists());

        return $code;
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            if (empty($coupon->code)) {
                $coupon->code = $coupon->generateUniqueCode();
            }
        });
    }
}