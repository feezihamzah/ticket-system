<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Services\ReservationService;
use App\Repositories\SeatRepository;

class SeatController extends Controller
{
    protected $service;
    protected $seatRepo;

    public function __construct(
        ReservationService $service,
        SeatRepository $seatRepo
    ) {
        $this->service = $service;
        $this->seatRepo = $seatRepo;
    }

    // show page
    public function index()
    {
        $events = Event::select('id','name')->get();
        return view('seat.index', compact('events'));
    }
    
    // get seats
    public function getSeats($eventId)
    {
        return $this->seatRepo->getByEvent($eventId);
    }

    // reserve
    public function reserve(Request $request, $eventId)
    {
        return $this->service->reserve($eventId, $request->input('seats'));
    }

    // confirm
    public function confirm($reservationId)
    {
        return $this->service->confirm($reservationId);
    }

    public function cancel($reservationId)
    {
        return $this->service->cancel($reservationId);
    }
}
