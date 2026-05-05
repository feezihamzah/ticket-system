<?php

namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository
{
    public function create($eventId)
    {
        return Reservation::create([
            'event_id' => $eventId,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    public function delete($reservation)
    {
        return $reservation->delete();
    }

    public function attachSeats($reservation, $seats)
    {
        $reservation->seats()->attach($seats->pluck('id'));
    }

    public function findWithSeats($id)
    {
        return Reservation::with('seats')->findOrFail($id);
    }
}