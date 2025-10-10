<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\DeleteCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::all();
        return ResponseBuilder::success(CouponResource::collection($coupons));
    }
    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return ResponseBuilder::success(new CouponResource($coupon));
    }
    public function create(CreateCouponRequest $request)
    {
        $coupon = Coupon::create($request->only([
            'code',
            'type',
            'value',
            'usage_limit',
            'usage_limit_per_user',
            'starts_at',
            'expires_at',
            'is_active',
            'user_id',
            'category_id',
            'product_id',
            'min_order_amount',
            'max_discount_amount',
        ]));
        return ResponseBuilder::success(new CouponResource($coupon));
    }
    public function update(UpdateCouponRequest $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->only([
            'code',
            'type',
            'value',
            'usage_limit',
            'usage_limit_per_user',
            'starts_at',
            'expires_at',
            'is_active',
            'user_id',
            'category_id',
            'product_id',
            'min_order_amount',
            'max_discount_amount',
        ]));
        return ResponseBuilder::success(new CouponResource($coupon));
    }
    public function delete(DeleteCouponRequest $request)
    {
        $coupon = Coupon::findOrFail($request->coupon_id);
        if ($coupon->usages()->count() > 0) {
            return ResponseBuilder::error([], 'Bu kuponla ilişkili kullanım kayıtları mevcut. Lütfen tüm kullanım kayıtlarını silin.', 400);
        }
        $coupon->delete();
        return ResponseBuilder::success(null);
    }
}
