<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\GenerateTicketPdfAndSendEmail;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
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
                'uuid' => (string) Str::uuid(),
            ]);
        }

        $order->refresh();

        GenerateTicketPdfAndSendEmail::dispatch($order);

        return response()->json([
            'order' => $order,
        ], 201);
    }
}
