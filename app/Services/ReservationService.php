<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\SeatRepository;
use App\Repositories\ReservationRepository;
use App\Models\Reservation;

class ReservationService
{
    protected $seatRepo;
    protected $reservationRepo;

    public function __construct(
        SeatRepository $seatRepo,
        ReservationRepository $reservationRepo
    ) {
        $this->seatRepo = $seatRepo;
        $this->reservationRepo = $reservationRepo;
    }

    // RESERVE SEATS
    public function reserve($eventId, $seatNumbers)
    {
        return DB::transaction(function () use ($eventId, $seatNumbers) {

            $seats = $this->seatRepo->lockSeats($eventId, $seatNumbers);

            if ($seats->count() !== count($seatNumbers)) {
                return response()->json([
                    'message' => 'Some seats not found'
                ], 400);
            }

            foreach ($seats as $seat) {
                if ($seat->status !== 'available') {
                    return response()->json([
                        'message' => "Seat {$seat->seat_number} not available"
                    ], 400);
                }
            }

            $reservation = $this->reservationRepo->create($eventId);

            $this->seatRepo->markReserved($seats->pluck('id'));

            $this->reservationRepo->attachSeats($reservation, $seats);

            return response()->json([
                'reservation_id' => $reservation->id,
                'expires_at' => $reservation->expires_at
            ]);
        });
    }

    // CONFIRM BOOKING
    public function confirm($reservationId)
    {
        return DB::transaction(function () use ($reservationId) {

            $reservation = $this->reservationRepo->findWithSeats($reservationId);

            if (!$reservation) {
                return response()->json([
                    'message' => 'Reservation not found'
                ], 404);
            }

            if ($reservation->status !== 'pending') {
                return response()->json([
                    'message' => 'Invalid reservation state'
                ], 400);
            }

            if ($reservation->expires_at < now()) {
                return response()->json([
                    'message' => 'Reservation expired'
                ], 400);
            }

            // mark seats sold
            $this->seatRepo->markSold($reservation->seats->pluck('id'));

            // update reservation
            $reservation->update([
                'status' => 'confirmed'
            ]);

            return response()->json([
                'message' => 'Booking successful'
            ]);
        });
    }

    // CANCEL RESERVATION
    public function cancel($reservationId)
    {
        return DB::transaction(function () use ($reservationId) {

            $reservation = $this->reservationRepo->findWithSeats($reservationId);

            if (!$reservation) {
                return response()->json([
                    'message' => 'Reservation not found'
                ], 404);
            }

            if ($reservation->status !== 'pending') {
                return response()->json([
                    'message' => 'Cannot cancel this reservation'
                ], 400);
            }

            // release seats
            $this->seatRepo->markAvailable($reservation->seats->pluck('id'));

            // delete reservation
            $this->reservationRepo->delete($reservation);

            return response()->json([
                'message' => 'Reservation cancelled successfully'
            ]);
        });
    }
}