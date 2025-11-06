<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $seats = Seat::whereIn('id', $request->seat_ids)->get();
        $totalPrice = $seats->sum('price');

        $order = Order::create([
            'schedule_id' => $request->schedule_id,
            'email' => $request->email,
            'total_price' => $totalPrice,
        ]);

        foreach ($seats as $seat) {
            Ticket::create([
                'order_id' => $order->id,
                'seat_id' => $seat->id,
                'schedule_id' => $request->schedule_id,
                'ticket_used' => false,
            ]);
        }

        return response()->json([
            'order' => $order,
        ], 201);
    }
}
