<?php

namespace App\Http\Resources;

use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Seat
 */
class SeatResource extends JsonResource
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
            'hall_id' => $this->hall_id,
            'row' => $this->row,
            'column' => $this->column,
            'seat_name' => $this->seat_name,
            'price' => number_format($this->price / 100, 2, '.', ''),
        ];
    }
}
