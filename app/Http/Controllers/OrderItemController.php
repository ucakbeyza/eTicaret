<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseBuilder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Resources\OrderItemResource;

class OrderItemController extends Controller
{
    public function index(Request $request, $orderId)
    {
        $userId = $request->user()->id;
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->firstOrFail();
            
        $orderItems = $order->order_items()->with('product')->get();
        
        return ResponseBuilder::success(
            OrderItemResource::collection($orderItems), 
            'Sipariş alt detayları'
        );
    }
}