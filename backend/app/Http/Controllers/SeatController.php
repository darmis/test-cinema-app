<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeatResource;
use App\Models\Cart;
use App\Models\CartItems;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Seat $seat)
    {
        //
    }

    public function seatsBySchedule(Schedule $schedule): AnonymousResourceCollection
    {
        $seats = Seat::query()
            ->where('hall_id', $schedule->hall_id)
            ->orderBy('row')
            ->orderBy('column')
            ->get();

        return SeatResource::collection($seats);
    }

    public function takenSeats(Request $request, Schedule $schedule): JsonResponse
    {
        $userSeatIds = [];
        $userCart = null;

        // current user reserved seats
        if ($request->has('user_token') && $request->user_token) {
            $userCart = Cart::where('user_token', $request->user_token)
                ->where('schedule_id', $schedule->id)
                ->first();

            if ($userCart) {
                $userSeatIds = CartItems::where('cart_id', $userCart->id)
                    ->where('schedule_id', $schedule->id)
                    ->pluck('seat_id')
                    ->toArray();
            }
        }

        // other users reserved seats
        $otherCartSeatIdsQuery = CartItems::where('schedule_id', $schedule->id);

        if ($userCart) {
            $otherCartSeatIdsQuery->where('cart_id', '!=', $userCart->id);
        }

        $otherCartSeatIds = $otherCartSeatIdsQuery->pluck('seat_id')->toArray();

        // already bought seats
        $ticketSeatIds = Ticket::where('schedule_id', $schedule->id)
            ->pluck('seat_id')
            ->toArray();

        $otherTakenSeatIds = array_unique(array_merge($otherCartSeatIds, $ticketSeatIds));

        return response()->json([
            'user_seat_ids' => array_values($userSeatIds),
            'other_taken_seat_ids' => array_values($otherTakenSeatIds),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seat $seat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seat $seat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seat $seat)
    {
        //
    }
}
