<?php

namespace App\Repositories;

use App\Models\Seat;

class SeatRepository
{
    // get seats
    public function getByEvent($eventId)
    {
        return Seat::where('event_id', $eventId)
            ->select('id', 'seat_number', 'status')
            ->orderBy('id')
            ->get();
    }

    // lock seats for reservation 
    public function lockSeats($eventId, $seatNumbers)
    {
        return Seat::where('event_id', $eventId)
            ->whereIn('seat_number', $seatNumbers)
            ->lockForUpdate()
            ->get();
    }

    // bulk update to reserved
    public function markReserved($seatIds)
    {
        return Seat::whereIn('id', $seatIds)
            ->update(['status' => 'reserved']);
    }

    // bulk update to available
    public function markAvailable($seatIds)
    {
        return Seat::whereIn('id', $seatIds)
            ->update(['status' => 'available']);
    }

    // bulk update to sold
    public function markSold($seatIds)
    {
        return Seat::whereIn('id', $seatIds)
            ->update(['status' => 'sold']);
    }

    // get seat event stats
    public function getEventStats($eventId)
    {
        $total = Seat::where('event_id', $eventId)->count();

        $reserved = Seat::where('event_id', $eventId)
            ->where('status', 'reserved')
            ->count();

        $sold = Seat::where('event_id', $eventId)
            ->where('status', 'sold')
            ->count();

        return [
            'total' => $total,
            'reserved' => $reserved,
            'sold' => $sold,
            'available' => $total - $reserved - $sold,
        ];
    }
}