<!DOCTYPE html>
<html>
<head>
    <title>Ticket Booking</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app-container">

    <div class="left-panel">
        <div id="eventArea">
            <h3>Select Event</h3>
            <select id="eventSelect" onchange="loadSeats()" class="select-box">
                <option value="">-- Select Event --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
        <div id="bookingArea" style="display:none;">
            <div class="legend">
                <div class="legend-item">
                    <span class="seat available"></span> Available
                </div>
                <div class="legend-item">
                    <span class="seat selected"></span> Selected
                </div>
                <div class="legend-item">
                    <span class="seat reserved"></span> Reserved
                </div>
                <div class="legend-item">
                    <span class="seat sold"></span> Sold
                </div>
            </div>
            <div class="screen">STAGE</div>
            <div id="seats"></div>
        </div>
    </div>

    <div id="summaryArea" style="display:none;" class="right-panel">
        <h3>Booking Summary</h3>

        <p><strong>Event:</strong></p>
        <p id="eventName">-</p>

        <p><strong>Selected Seats:</strong></p>
        <p id="selectedSeatsText">-</p>

        <p><strong>Timer:</strong></p>
        <p id="timer">-</p>

        <button id="reserveBtn" onclick="reserveSeats()" class="btn-primary">Reserve</button>
        <button id="confirmBtn" onclick="confirmBooking()" class="btn-success" style="display:none;">
            Confirm Booking
        </button>
    </div>

    <div id="successArea" style="display:none;" class="success-card">
        <h2>🎉 Booking Successful!</h2>

        <p id="successEvent"></p>
        <p id="successSeats"></p>

        <button onclick="goBack()">Back to Select Event</button>
    </div>

</div>
</body>
</html>