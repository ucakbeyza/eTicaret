<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'product' => [
                'id' => $this->product_id,
                'name' => $this->product_name_snapshot,
                'price'=> $this->product_price_snapshot,
            ],
            'line_total' => $this->line_total,
        ];

    }
}