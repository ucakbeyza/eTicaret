<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'name' => $this->product->name,
            'price' => $this->product->price,
            'currency' => $this->product->currency,
            'quantity' => $this->quantity,
            'user_id' => $this->user_id,
            'subtotal' => $this->subtotal,
        ];

    }
}
