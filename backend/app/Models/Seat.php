<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    /** @use HasFactory<\Database\Factories\SeatFactory> */
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'seat_name',
        'price',
        'row',
        'column',
    ];
}
