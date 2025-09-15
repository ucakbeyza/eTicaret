<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_no' => $this->order_no,
            'total_price'=> $this->total_price,
            'currency' => $this->currency,
            'status' => $this->status,
            'paid_at'=> $this->paid_at,
            'order_items_count' => $this->order_items_count ?? 0,
            'buyer_name' => optional($this->user)->first_name . ' ' . optional($this->user)->last_name,
        ];
    }
}