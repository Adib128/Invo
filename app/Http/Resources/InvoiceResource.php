<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'dueDate' => Carbon::parse($this->dueDate)->format('m-d-Y'),
            'subTotal' => $this->subTotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'customer' => new CustomerResource($this->customer),
        ];
    }
}
