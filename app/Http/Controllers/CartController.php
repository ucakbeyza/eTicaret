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
use App\Services\PaymentService;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;


class CartController extends Controller
{
    public function add(AddCartRequest $request)//sepete ekleme
    {   
        $product = Product::find($request->product_id);
        if (!$product) {
            return ResponseBuilder::error(null, 'Ürün bulunamadı', 404);
        }
        if($product->stock < $request->quantity) {
            return ResponseBuilder::error(null,'Stokta yeterli ürün yok');
        }
        //yeni cart kaydı için
        $cart = Cart::create(array_merge(
            $request->only([
            'product_id',
            'quantity'
        ]), [
            'user_id' => $request->user()->id
            ]
        ));
        //güncel sepet döner
        return $this->getUserCart($request->user()->id, 'Ürün sepete eklendi');
    }
    public function update(UpdateCartRequest $request)//sepet güncelleme
    {
        $product = Product::find($request->product_id);
        if (!$product) {
            return ResponseBuilder::error(null, 'Ürün bulunamadı', 404);
        }
        if($product->stock < $request->quantity) {
            return ResponseBuilder::error(null,'Stokta yeterli ürün yok');
        }
        //x kullanıcısı için y ürününe ait sepet kaydı var mı bunu arama
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
    public function delete(DeleteCartRequest $request)//ürün silme
    {
        $cart = Cart::query()//ürünü bul
            ->where('product_id',$request->product_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart) {//yoksa hata
            return ResponseBuilder::error(null, 'Sepette bu ürün yok');
        }

        $cart->delete();//sil
        
        return $this->getUserCart($request->user()->id, 'Ürün sepetten silindi');//güncel sepeti dön
    }
    public function show(Request $request)
    {
        return $this->getUserCart($request->user()->id, 'Sepetiniz listelendi');
    }
    public function checkout(CheckoutRequest $request)
    {
        $userId = $request->user()->id;
        $cartItems = $this->getCartItems($userId);

        if ($cartItems->isEmpty()) {
            return ResponseBuilder::error(null, 'Sepet boş', 422);
        }

        $total = $this->calculateTotalPrice($cartItems) ?? 0;
        $currency = $cartItems->first()->product->currency ?? 'TRY';
        
        //sipariş numarası oluşturma
        $orderNo = 'ORD-' . $userId . '-' . str_pad((string) now()->format('YmdHis'), 10, '0', STR_PAD_LEFT);

        $payload = [
            'name' => $request->input('name'),
            'card_number' => $request->input('card_number'),
            'expiry_date' => $request->input('expiry_date'),
            'cvv' => $request->input('cvv'),
            'amount' => $total,
            'currency' => $currency,
            'order_id' => $orderNo,
            'description' => 'ürün satın alma',
        ];

        // Önce pending sipariş kaydı oluştur
        $order = DB::transaction(function () use ($userId, $orderNo, $total, $currency) {
            return Order::create([
                'user_id' => $userId,
                'order_no' => $orderNo,
                'total_price' => $total,
                'currency' => $currency,
                'status' => 'pending',
            ]);
        });

        // Ödeme isteğini gönder
        $paymentService = new PaymentService();
        $result = $paymentService->charge($payload);

        if (!$result['success']) {
            // Ödeme başarısız → order failed
            Order::where('id', $order->id)->update(['status' => 'failed']);
            return ResponseBuilder::error($result['message'], 'Payment failed', 402);
        }

        // Ödeme başarılı → order paid ve kalemleri yaz, stok düş, sepeti boşalt
        try {
            DB::transaction(function () use ($order, $cartItems, $userId) {
                // Order status güncelle
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                foreach ($cartItems as $cartItem) {
                    // Stok için satırı kilitleyerek oku
                    $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \RuntimeException('Ürün bulunamadı');
                    }

                    if ($product->stock < $cartItem->quantity) {
                        throw new \RuntimeException('Yetersiz stok: ' . $product->name);
                    }

                    // Stok düş
                    $product->update([
                        'stock' => $product->stock - $cartItem->quantity,
                    ]);

                    // Sipariş kalemi oluştur
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name_snapshot' => $product->name,
                        'product_price_snapshot' => $product->price,
                        'quantity' => $cartItem->quantity,
                        'line_total' => round($product->price * $cartItem->quantity, 2),
                    ]);
                }

                // Sepeti boşalt
                Cart::where('user_id', $userId)->delete();
            });
        } catch (\Throwable $e) {
            // Yetersiz stok veya başka bir hata: siparişi failed yap
            Order::where('id', $order->id)->update(['status' => 'failed']);
            return ResponseBuilder::error(null, $e->getMessage(), 422);
        }

        return ResponseBuilder::success([
            'orderId' => $orderNo,
            'price' => $total,
            'currency' => $currency,
        ]);
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
            "total_price" => $this->calculateTotalPrice($cartItems) ?? 0,
            "currency" => $cartItems->first()->product->currency ?? 'TRY',
        ], $message);
    }

}
