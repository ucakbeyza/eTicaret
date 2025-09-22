<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseBuilder;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderItemResource;

class OrderController extends Controller
{
    //tüm siparişler
    public function index(Request $request){
        $userId = $request->user()->id;
        $orders = Order::where('user_id', $userId)
            ->withCount('order_items')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseBuilder::success(OrderResource::collection($orders), 'Siparişleriniz');
    }
    //belli bir sipariş
    public function show(Request $request, $id){
        $userId = $request->user()->id;
        
        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->withCount('order_items')
            ->firstOrFail();
            
        $orderItems = $order->order_items()->with('product')->get();
        
        return ResponseBuilder::success([
            'order' => new OrderResource($order),
            'items' => OrderItemResource::collection($orderItems)
        ], 'Sipariş detayları');
    }
}
