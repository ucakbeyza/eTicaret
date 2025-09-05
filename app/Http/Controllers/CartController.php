<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseBuilder;
use App\Models\Cart;    
use App\Http\Resources\CartItemResource;
use App\Http\Requests\AddCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Requests\DeleteCartRequest;    
use App\Models\Product;


class CartController extends Controller
{
    public function add(AddCartRequest $request)
    {   
        $product = Product::find($request->product_id);

        if($product->stock < $request->quantity) {
            return ResponseBuilder::error(null,'Stokta yeterli ürün yok');
        }

        $cart = Cart::create(array_merge(
            $request->only([
            'product_id',
            'quantity'
        ]), [
            'user_id' => $request->user()->id
            ]
        ));
        return $this->getUserCart($request->user()->id, 'Ürün sepete eklendi');
    }

    public function update(UpdateCartRequest $request)
    {
        $product = Product::find($request->product_id);

        if($product->stock < $request->quantity) {
            return ResponseBuilder::error(null,'Stokta yeterli ürün yok');
        }
        $cart = Cart::query()///dikkat
            ->where('product_id',$request->product_id)
            ->where('user_id', $request->user()->id)
            ->first();
        
        if (!$cart) {
            return ResponseBuilder::error(null, 'Sepette bu ürün yok');
        }
        
        $cart->update($request->only([
            'quantity'
        ]));
        return $this->getUserCart($request->user()->id, 'Sepet güncellendi');
        
    }
    public function delete(DeleteCartRequest $request)
    {
        $cart = Cart::query()
            ->where('product_id',$request->product_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart) {
            return ResponseBuilder::error(null, 'Sepette bu ürün yok');
        }

        $cart->delete();

        return $this->getUserCart($request->user()->id, 'Ürün sepetten silindi');
    }
    public function list(Request $request)
    {
        return $this->getUserCart($request->user()->id, 'Sepetiniz listelendi');
    }
    // Clear cart (Extra attribute)
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return $this->getUserCart($request->user()->id, 'Sepetiniz temizlendi');
    }
    private function getCartItems($userId)
    {
        return Cart::query()
            ->with(['product'])
            ->where('user_id', $userId)->get();
    }
    private function calculateTotalPrice($cartItems)
    {
        return $cartItems->sum(function($item) {
            return $item->subtotal;
        });
    }
    private function getUserCart($userId, $message)
    {
        $cartItems = $this->getCartItems($userId);

        if ($cartItems->isEmpty()) {
            return ResponseBuilder::success([
                "items" => [],
                "total_price" => 0,
                "currency" => "TRY"
            ], $message);
        }
        
        return ResponseBuilder::success([
            "items" => CartItemResource::collection($cartItems),
            "total_price" => $this->calculateTotalPrice($cartItems),
            "currency" => $cartItems->first()->product->currency
        ], $message);
    }

}
