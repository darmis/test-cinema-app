<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use App\Models\Seat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'user_token' => 'required|string',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $cart = Cart::firstOrCreate(
            ['user_token' => $request->user_token],
            ['schedule_id' => $request->schedule_id, 'total_price' => 0]
        );

        if ($cart->schedule_id !== (int) $request->schedule_id) {
            CartItems::where('cart_id', $cart->id)->delete();
            $cart->update([
                'schedule_id' => $request->schedule_id,
                'total_price' => 0,
            ]);
        }

        return response()->json([
            'cart' => $cart,
        ]);
    }

    public function getCart(Request $request): JsonResponse
    {
        $request->validate([
            'user_token' => 'required|string',
        ]);

        $cart = Cart::with('items.seat')->where('user_token', $request->user_token)->first();

        if (! $cart) {
            return response()->json(['cart' => null]);
        }

        return response()->json([
            'cart' => $cart,
            'schedule_id' => $cart->schedule_id,
            'seat_ids' => $cart->items->pluck('seat_id')->toArray(),
        ]);
    }

    public function updateCartItems(Request $request): JsonResponse
    {
        $request->validate([
            'user_token' => 'required|string',
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'array',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        $cart = Cart::where('user_token', $request->user_token)->first();

        if (! $cart && empty($request->seat_ids)) {
            return response()->json([
                'cart' => null,
                'total_price' => 0,
            ]);
        }

        if (! $cart) {
            $cart = Cart::create([
                'user_token' => $request->user_token,
                'schedule_id' => $request->schedule_id,
                'total_price' => 0,
            ]);
        }

        CartItems::where('cart_id', $cart->id)->delete();

        if (! empty($request->seat_ids)) {
            foreach ($request->seat_ids as $seatId) {
                CartItems::create([
                    'cart_id' => $cart->id,
                    'seat_id' => $seatId,
                    'schedule_id' => $request->schedule_id,
                ]);
            }
        }

        $totalPrice = 0;
        if (! empty($request->seat_ids)) {
            $seats = Seat::whereIn('id', $request->seat_ids)->get();
            $totalPrice = $seats->sum('price');
        }

        $cart->total_price = $totalPrice;
        $cart->save();

        return response()->json([
            'cart' => $cart->load('items'),
            'total_price' => $cart->total_price,
        ]);
    }
}
