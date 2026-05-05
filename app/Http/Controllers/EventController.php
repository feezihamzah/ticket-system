<?php

namespace App\Http\Controllers;

use App\Models\Seat;

class EventController extends Controller
{
    public function show($eventId)
    {
        $total = Seat::where('event_id', $eventId)->count();

        $reserved = Seat::where('event_id', $eventId)
            ->where('status', 'reserved')
            ->count();

        $sold = Seat::where('event_id', $eventId)
            ->where('status', 'sold')
            ->count();

        $available = $total - $reserved - $sold;

        return response()->json([
            'total' => $total,
            'reserved' => $reserved,
            'sold' => $sold,
            'available' => $available,
        ]);
    }
}