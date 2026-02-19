<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total' => $this->total,
            'address' => $this->address,
            'payment_method' => $this->payment_method,
            'items' => OrderItemResource::collection($this->items),

            // Retornar as duas versões
            'created_at' => $this->created_at, // para o Vue formatar conforme necessário
            'created_at_formatted' => $this->created_at->format('d/m/Y H:i'), // Formatação preferencial
        ];
    }
}